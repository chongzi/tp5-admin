<?php

namespace app\api\controller\v1;

use app\api\controller\Base;

class Slide extends Base
{

    /**
     * 定义开放接口，无需认证即可访问的接口
     * @var array
     */
    protected $publicActions = [
        'index',
    ];

    public function index()
    {

        $map = [];
        if (isset($this->data['group'])) {
            $map['group'] = $this->data['group'];
        }

        $list = logic('slide')->search($map, 'sort desc', 1, 10);
        if ($list) {
            $list = $this->slideListFormat($list);
            return $this->apkReturn($list);
        }
        return $this->apkReturn([]);
    }

    // 非接口方法
    /*
     *      Book 信息格式化
     */
    protected function slideListFormat($list)
    {
        if (empty($list)) {
            return [];
        }

        foreach ($list as &$slide) {
            $slide = $this->slideFormat($slide);
        }
        return $list;
    }

    protected function slideFormat($slide)
    {

        return [
            'id' => $slide['id'],
            'title' => $slide['title'],
            'img' => $this->realPath($slide['img']),
            'group' => $slide['group'],
            'type' => $slide['type'],
            'link' => $slide['link'],
            'sort' => $slide['sort'],
            'add_time' => $slide['add_time'],
            'add_date' => $slide['add_date'],
        ];
    }

}
