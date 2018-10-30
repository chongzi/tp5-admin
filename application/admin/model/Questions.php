<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class Questions extends BaseModel
{
    //新增和更新自动完成
    protected $auto = [];
    protected $insert = [];  
    protected $update = [];  

    protected function initialize($model='',$class='')
    {
        $arr = explode("\\", __CLASS__);
        //$model = "admin/".$arr[count($arr)-1];//获取当前模型
        $model = $arr[count($arr)-1];//获取当前模型
        parent::initialize($model,__CLASS__);
    }

    public function viewListBy($where=null,$field='')
    {
        $res['list'] = Db::table('s_questions')->field($field)->where(['app_id'=>$where['app_id'],'status'=>$where['status']])->order('sort desc')->select();
        $res['questions_id'] = Db::table('s_user_questions')->field('questions')->where(['user_id'=>$where['user_id'],'app_id'=>$where['app_id']])->select();
        return $res;
    }

    //列表 带分页
    public function search($search,$nolimit = false){
        if(!empty($search)){
            if(is_numeric($search['title'])){
                $where = 'a.id = '.$search['title'];
            }else{
                $where = "a.name like '%".$search['title']."%'";
            }
            if($search['app_id']){
                if($where != ''){
                    $where .= ' and a.app_id = '.$search['app_id'];
                }else{
                    $where .= 'a.app_id = '.$search['app_id'];
                } 
            }
        }else{
            $where = '';
        }
        if($nolimit){
            return Db::table('s_questions a')->field('a.id')->join('s_app b','a.app_id = b.id')->where($where)->count();
        }else{
            return Db::table('s_questions a')->field('a.*,b.title')->join('s_app b','a.app_id = b.id')->order('a.id desc')->where($where)->paginate(config('admin_pages_num'));
        }

    }

    //储存用户题目信息
    //
    public function saveQuestionsInfo($user_id,$app_id,$idArr){
        Db::table('s_user_questions')->insert(['user_id'=>$user_id,'app_id'=>$app_id,'questions'=>$idArr,'add_time'=>time()]);
    }

    public function selQuestionInfo($ids,$data){
        if($ids){
            $sql = "select id,answer,name,options from s_questions where app_id = ".$data['app_id']." and id not in(".$ids.') order by rand() limit 1';
            $arr = Db::query($sql)[0];
            $d['questions'] = $ids.','.$arr['id'];
        }else{
            $sql = "select id,answer,name,options from s_questions where app_id = ".$data['app_id']. ' order by rand() limit 1';
            $arr = Db::query($sql)[0];
            $d['questions'] = $arr['id'];
        }
        
        $d['upd_time'] = time();
        Db::table('s_user_questions')->where(['user_id'=>$data['user_id'],'app_id'=>$data['app_id']])->update($d);
        return $arr;
    }

    public function randQuestionInfo($data){
        $arr = Db::table('s_questions')->field('id,answer,name,options')->where(['app_id'=>$data['app_id']])->order('rand()')->find();
        $d['user_id'] = $data['user_id'];
        $d['app_id'] = $data['app_id'];
        $d['questions'] = $arr['id'];
        $d['add_time'] = time();
        $d['upd_time'] = time();
        Db::table('s_user_questions')->insert($d);
        return $arr;
    }

    public function emptyQuestion($data){
        $d['questions'] = 0;
        // var_dump($data);exit;
        return  Db::table('s_user_questions')->where(['user_id'=>(int)$data['user_id'],'app_id'=>(int)$data['app_id']])->update($d);
    }   

    public function getAnswerMoney($data){
        return Db::table('s_user_questions')->field('questions')->where(['user_id'=>(int)$data['user_id'],'app_id'=>(int)$data['app_id']])->find()['questions'];
    }
}