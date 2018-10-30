<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class Wxgame extends BaseModel
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

    public function getUserInfo($data)
    {
        $info['nick_name'] = $data['nickName'];
        $info['union_id'] = $data['openid'];
        $info['reg_ip'] = $data['ip'];
        $info['face'] = $data['avatarUrl'];
        $info['app_id'] = $data['app_id'];
        $info['reg_time'] = time();
        $info['reg_date'] = date("Ymd");
        $id = Db::table('s_user')->field('id')->where('union_id',$data['openid'])->find()['id'];
        if($id){
            Db::table('s_user')->where('union_id',$data['openid'])->update($info);
        }else{
            $id = Db::table('s_user')->insertGetId($info);
        }
        return ['user_id'=>$id];
    }

   
}