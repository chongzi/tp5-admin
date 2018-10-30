<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class OrderChange extends BaseLogic
{
    /*******************************基础配置开始*******************************/
    
    public $flag_opt = array();
    public $id_alias = "gift_id";//主表与其他表关联的字段
    private $flag_model = "";//标记模型
    private $slave_model = "";//从表模型
    private $slave_relation_field = "";//从表和主表关联字段

    /*******************************基础配置结束*******************************/
    public $opt_status = array(
        1 => '<span style="color:green">上架</span>',
        0 => '<span style="color:#CAA">下架</span>',
    );
    
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

    public function changeMoney($data){
        $app_id = $data['app_id'];
        $user_id = $data['user_id'];
        $money = (float)$data['money'];
        $app_info = model('app')->info($app_id);
        $allow_change_money_arr = explode('/',$app_info['allow_change_money']);
        if(count($allow_change_money_arr) > 1){
            $order_change_info = model('order_change')->listBy(['app_id'=>$app_id,'user_id'=>$user_id],'id');
            if($order_change_info == false){
                $n = 0;
            }else{
                $n = count($order_change_info);
            }
            if(count($order_change_info) > count($allow_change_money_arr)){
                $allow_change_money = (float)$allow_change_money_arr[count($allow_change_money_arr) - 1];
            }else{
                $allow_change_money = (float)$allow_change_money_arr[$n];
            }
        }else{
            $allow_change_money = (float)$allow_change_money_arr[0];
        }

        if($money >= $allow_change_money){
            $user_app_info = model('user_app')->infoBy(['user_id'=>$user_id,'app_id'=>$app_id],'id,money');
            $f_money = $money * $app_info['convert_gift_rate'];
            if($user_app_info['money'] >= $money){
                $d['app_id'] = $app_id;
                $d['user_id'] = $user_id;
                $d['uniqid'] = $this->getUniqid();
                $d['money'] = $f_money;
                $d['ip'] = $data['ip'];
                $d['add_time'] = time();
                $d['change_num'] = $n + 1;
                $d['use_money'] = $money;
                 // 启动事务
                Db::startTrans();
                try{
                    Db::table('s_user_app')->where('id',$user_app_info['id'])->update(['money'=>$user_app_info['money'] - $money]);
                    Db::table('s_order_change')->insert($d);
                    // 提交事务
                    Db::commit(); 
                    return ['msg'=>'success','data'=>['change_code'=>$d['uniqid'],'f_money'=>$user_app_info['money'] - $money,'change_money'=>$f_money,'use_money'=>$money]];   
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    $return['msg'] = json_encode($e);
                }
            }else{
                $return['msg'] = '你的余额不足,继续努力';
            }
        }else{
            $return['msg'] = '提现金额不足限定值:'.$allow_change_money;
        }
        return $return;
    }

    public function getUniqid(){
        return 'T'.uniqid();
    }
}