<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class ShareInfo extends BaseModel
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

    public function viewListBy($where=null,$field='',$limit=100,$order='id desc')
    {
        $giftId = Db::table('s_app')->field('app_gift_id')->where('id = '.$where['app_id'])->find()['app_gift_id'];
        $res = Db::table('s_gift')->field($field)->where('status = 1 and id in ('.$giftId.')')->order('sort desc')->select();
        return $res;
    }

    public function getShareNum($data){
        $sql = "select id from s_share_info where user_id = ".$data['user_id']." and app_id = ".$data['app_id']." and FROM_UNIXTIME(add_time,'%Y%m%d') = curdate()";
        return count(Db::query($sql));
    }

    public function checkQunday($data,$qun_id){
        $sql = "select id from s_share_info where qun_id = '".$qun_id."' and user_id = ".$data['user_id']." and app_id = ".$data['app_id']." and FROM_UNIXTIME(add_time,'%Y%m%d') = curdate()";
        return count(Db::query($sql));
    }
}