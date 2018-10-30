<?php

namespace app\api\controller\v1;

use app\api\controller\Base;

class Book extends Base
{
    private $book_ids = [];
    private $version_id_list = [];
    private $flag_opt = [];

    private $period_opt = [];
    private $grade_opt = [];
    private $part_type_opt = [];

    private $is_favorite = false;

    /**
     * 定义开放接口，无需认证即可访问的接口
     * @var array
     */
    protected $publicActions = [
        'version',
        'index',
        'answer',
        'tag',
        'tip',
    ];

    public function add()
    {
        echo THINK_VERSION;exit();

        $res = service("book")->test();
        $data["fun"] = $res;
        $data["a"] = 'bbb';
        $this->apkReturn($data);
    }

    public function version()
    {
        $version_map = logic('version')->versionMap([]);
        $this->bookAttributeOption();
        $subject_map = config('subject_list');

        $data = [
            'version' => $this->arrayToObject($version_map),
            'subject' => $this->arrayToObject($subject_map),
            'grade' => $this->arrayToObject($this->grade_opt),
            'part_type' => $this->arrayToObject($this->part_type_opt),

            'flag' => $this->arrayToObject($this->flag_opt),
        ];

        return $this->apkReturn($data);
    }

    public function index()
    {
        $page = isset($this->data['page']) ? $this->data['page'] : 1;
        $limit = isset($this->data['limit']) ? $this->data['limit'] : 10;

        $this->bookAttributeOption();
        $map = [
            'answer_imgs' => ['<>', ''],
        ];
        if (isset($this->data['name'])) {
            $map['name'] = $this->data['name'];
        }
        if (isset($this->data['code'])) {
            $map['code'] = $this->data['code'];
        }
        if (isset($this->data['year'])) {
            $map['year'] = $this->data['year'];
        }

        if (isset($this->data['version_id'])) {
            $map['version_id'] = $this->data['version_id'];
        } elseif (isset($this->data['version'])) {
            $version = logic('version')->infoBy(['name' => $this->data['version']]);
            if($version){
                $map['version_id'] = $version['id'];
            }
            
        }

        if (isset($this->data['grade_id'])) {
            $map['grade'] = $this->data['grade_id'];
        } elseif (isset($this->data['grade']) && array_search($this->data['grade'], $this->grade_opt) !== false) {
            $map['grade'] = array_search($this->data['grade'], $this->grade_opt);
        }

        if (isset($this->data['part_type_id'])) {
            $map['part_type'] = $this->data['part_type_id'];
        } elseif (isset($this->data['part_type']) && array_search($this->data['part_type'], $this->part_type_opt) !== false) {
            $map['part_type'] = array_search($this->data['part_type'], $this->part_type_opt);
        }

        $subject_map = config('subject_list');
        if (isset($this->data['subject_id'])) {
            $map['subject'] = $subject_map[$this->data['subject_id']];
        } elseif (isset($this->data['subject']) && in_array($this->data['subject'], $subject_map)) {
            $map['subject'] = $this->data['subject'];
        }

        if (isset($this->data['flag_id'])) {
            $map['flag_id'] = $this->data['flag_id'];
        } elseif (isset($this->data['flag'])) {
            if (array_search($this->data['flag'], $this->flag_opt) !== false) {
                $map['flag_id'] = array_search($this->data['flag'], $this->flag_opt);
            } else {
                $map['flag_id'] = 0;
            }
        }

        // 上报位置信息
        if (isset($this->data['user_id'])
            && isset($this->data['name'])
            && isset($this->data['latitude'])
            && isset($this->data['longitude'])) {

            $user_id = $this->data['user_id'];
            $name = $this->data['name'];
            $latitude = $this->data['latitude'];
            $longitude = $this->data['longitude'];

            logic('user_search')->record($user_id, $name, $latitude, $longitude);
        }

        $book_list = logic('book')->search($map, 'sort desc, id desc', $page, $limit, $count);
        if ($book_list) {
            $book_list = $this->bookListFormat($book_list);

            $data = [
                'count' => $count,
                'lists' => $book_list,
            ];
            return $this->apkReturn($data);
        }
        return $this->apkReturn(new \stdClass);
    }

    public function answer()
    {
        if (!isset($this->data['book_id'])) {
            return $this->apkReturn('参数错误', 0);
        }

        $book = logic('book')->info($this->data['book_id']);
        logic('book')->bookPvNumInc($book['id']);

        $book_list = [
            $book,
        ];
        $book_list = $this->bookListFormat($book_list, true);

        $book = array_pop($book_list);
        return $this->apkReturn($book);
    }

    public function share()
    {
        if (!isset($this->data['book_id'])) {
            return $this->apkReturn('参数错误', 0);
        }
        if (!isset($this->data['user_id'])) {
            return $this->apkReturn('参数错误', 0);
        }
        $book_id = $this->data['book_id'];
        $user_id = $this->data['user_id'];
        logic('book')->bookShareNumInc($book_id);
        if (logic('user_book')->auth($user_id, $book_id)) {
            return $this->apkReturn('操作成功');
        }
        return $this->apkReturn('操作失败', false);
    }

    public function favorite()
    {
        if (!isset($this->data['book_id'])) {
            return $this->apkReturn('参数错误', 0);
        }
        if (!isset($this->data['user_id'])) {
            return $this->apkReturn('参数错误', 0);
        }
        $book_id = $this->data['book_id'];
        $user_id = $this->data['user_id'];

        if (logic('user')->favorite($user_id, $book_id)) {
            return $this->apkReturn('操作成功');
        }
        return $this->apkReturn('操作失败', false);
    }

    public function myFavorite()
    {
        $page = isset($this->data['page']) ? $this->data['page'] : 1;
        $limit = isset($this->data['limit']) ? $this->data['limit'] : 10;
        if (!isset($this->data['user_id'])) {
            return $this->apkReturn('参数错误', 0);
        }
        $user_id = $this->data['user_id'];

        $this->is_favorite = true;

        $map = [
            'user_id' => $user_id,
        ];
        $myFavorite = logic('user_favorite')->search($map, 'add_time desc', $page, $limit, $count);
        $book_ids = array_column($myFavorite, 'book_id');

        if (!empty($book_ids)) {
            $map = [
                'id' => ['in', $book_ids],
            ];
            $book_list = logic('book')->search($map, 'id desc', 1, $limit);
        } else {
            $book_list = [];
        }

        if ($book_list) {
            $book_list = $this->bookListFormat($book_list);

            $data = [
                'count' => $count,
                'lists' => $book_list,
            ];
            return $this->apkReturn($data);
        }
        return $this->apkReturn(new \stdClass);
    }

    public function updAnswerImgs()
    {
        $mydata["id"] = $this->data['id'];
        $mydata["answer_imgs"] = $this->data['answer_imgs'];
        if($mydata["id"] && $mydata["answer_imgs"])
        {
            $res = logic('book')->upd($mydata);
        }
        if($res !== false)
        {
            return $this->apkReturn('操作成功',1);
        }
        else
        {
            return $this->apkReturn('操作失败', false);
        }
    }

    public function tag()
    {
        $tag = config('search_tag');
        return $this->apkReturn($tag);
    }

    public function tip()
    {
        $word = $this->data['word'];
        $list = BookSearchServer::tip($word);
        return $this->apkReturn($list);
    }

    // 非接口方法
    /*
     *      Book 信息格式化
     */
    protected function bookListFormat($list, $have_answer = false)
    {
        if (empty($list)) {
            return [];
        }
        $this->bookAttributeOption();

        foreach ($list as $book) {
            array_push($this->book_ids, $book['id']);
            array_push($this->version_id_list, $book['version_id']);
        }

        // 1.查询书本授权
        $access_book_ids = $this->accessQuery();

        // 2.查询版本名称
        $map = [
            'id' => ['in', $this->version_id_list],
        ];
        $version_map = logic('version')->versionMap($map);

        // 3.查询收藏
        $favorite_map = $this->favoriteQuery();

        // 4.查询flag
        $book_flag = $this->flagQuery();

        foreach ($list as &$book) {
            $book = $this->bookFormat($book, $access_book_ids, $version_map, $favorite_map, $book_flag, $have_answer);
        }
        return $list;
    }

    protected function bookAttributeOption()
    {
        $this->period_opt = model('version_grade')->period_opt;
        $this->grade_opt = model('version_grade')->grade_opt;
        $this->part_type_opt = model('version_grade')->part_type_opt;
        $this->flag_opt = model('book')->flag_opt;
        $this->subject_opt = config('subject_list');
    }

    protected function accessQuery()
    {
        if (!isset($this->data['user_id'])) {
            return [];
        }
        
        // 1. 以书本为单位，查询权限
        // $map = [
        //     'user_id' => $this->data['user_id'],
        //     'book_id' => ['in', $this->book_ids],
        // ];

        // return logic('user_book')->userBook($map);

        // 2. 改版：分享一次，即可查看全部答案
        $map = [
            'user_id' => $this->data['user_id'],
        ];

        if(logic('user_book')->userBook($map)){
            return $this->book_ids;
        }else{
            return [];
        }
    }

    protected function favoriteQuery()
    {
        if (!isset($this->data['user_id'])) {
            return [];
        }

        if ($this->is_favorite) {
            return $this->book_ids;
        }

        $map = [
            'user_id' => $this->data['user_id'],
            'book_id' => ['in', $this->book_ids],
        ];

        return logic('user_favorite')->favoriteBook($map);
    }

    protected function flagQuery()
    {
        return logic('book')->bookFlag($this->book_ids);
    }

    protected function getSex($book)
    {
        if (isset($book['sex'])) {
            return $book['sex'];
        }
        $hash = (int) md5($book['author']);
        return $hash > 5 ? 1 : 0;
    }

    

    protected function bookFormat($book, $access_book_ids = [], $version_map = [], $favorite_map = [], $book_flag = [], $have_answer = false)
    {

        $access = 0;
        $favorite = 0;
        $flag = [];
        $answer_list = [];
        if (!empty($access_book_ids)) {
            $access = (int) in_array($book['id'], $access_book_ids);
        }

        if (!empty($favorite_map)) {
            $favorite = (int) in_array($book['id'], $favorite_map);
        }

        if (!empty($book_flag) && isset($book_flag[$book['id']])) {
            $flag_ids = $book_flag[$book['id']];

            // PHP 5.6
            // $flag = array_filter($this->flag_opt, function ($flag_id) use ($flag_ids) {
            //     return in_array($flag_id, $flag_ids);
            // }, ARRAY_FILTER_USE_KEY);

            foreach ($this->flag_opt as $flag_id => $flag_name) {
                if (in_array($flag_id, $flag_ids)) {
                    $flag[] = [
                        'id' => $flag_id,
                        'name' => $flag_name,
                    ];
                }
            }
        }

        if ($have_answer) {
            if (strlen($book['answer_imgs']) > 0) {
                $answer_list = explode(',', $book['answer_imgs']);
            }
        }

        return [
            'id' => $book['id'],
            'name' => $book['name'],
            'cover_img' => $this->realPath($book['cover_img']),
            'year' => $book['year'],
            'subject' => $book['subject'],
            'press' => $book['press'],
            'code' => $book['code'],
            'is_del' => $book['is_del'],
            'sort' => $book['sort'],
            'share_num' => $book['share_num'],
            'pv_num' => $book['pv_num'],

            'version' => $version_map[$book['version_id']],

            'period' => $this->period_opt[$book['period']],
            'grade' => $this->grade_opt[$book['grade']],
            'part_type' => $this->part_type_opt[$book['part_type']],

            'access' => $access,
            'favorite' => $favorite,
            'flag' => $flag,
            'share_content' => '',

            'author' => $book['author'],
            'sex' => $this->getSex($book),
            'time' => $book['time'],

            'answer_list' => $answer_list,
        ];
    }

}
