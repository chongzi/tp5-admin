<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class GiftConvertLog extends BaseLogic
{
    /*******************************基础配置开始*******************************/
    
    public $flag_opt = array();
    public $id_alias = "gift_convert_log_id";//主表与其他表关联的字段
    private $flag_model = "";//标记模型
    private $slave_model = "";//从表模型
    private $slave_relation_field = "";//从表和主表关联字段

    /*******************************基础配置结束*******************************/

    function __construct()
    {
        $arr = explode("\\", __CLASS__);
        $model = $arr[count($arr)-1];//获取当前模型
        $mp["id_alias"] = $this->id_alias;
        $mp["slave_model"] = $this->slave_model;
        $mp["slave_relation_field"] = $this->slave_relation_field;
        $mp["flag_model"] = $this->flag_model;
        $mp["flag_opt"] = $this->flag_opt;
        parent::__construct($model,$mp);
    }

    public function changeGift($user_app_info,$gift_info,$money,$data){
       // 启动事务
        Db::startTrans();
        $info['app_id'] = $user_app_info['app_id'];
        $info['gift_id'] = $gift_info['id'];
        $info['user_id'] = $user_app_info['user_id'];
        $info['gift_name'] = $gift_info['name'];
        $info['gift_img'] = $gift_info['img'];
        $info['gift_desp'] = $gift_info['desp'];
        $info['money'] = $money;
        $info['gift_price'] = $gift_info['price'];
        $info['ip'] = $data['ip'];
        $info['add_time'] = time();
        $info['name'] = $data['name'];
        $info['addr'] = $data['addr'];
        $info['phone'] = $data['phone'];
        $info['status'] = 1;
        try{
            Db::table('s_user_app')->where('id',$user_app_info['id'])->update(['money'=>$user_app_info['money'] - $money]);
            Db::table('s_gift_convert_log')->insert($info);
            // 提交事务
            Db::commit(); 
            return true;   
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
    }
    
}