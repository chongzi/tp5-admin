<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class Orders extends BaseLogic
{
    /*******************************基础配置开始*******************************/
    
    public $flag_opt = array();
    public $id_alias = "orders_id";//主表与其他表关联的字段
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
    
    /**   
    * 根据订单号来获取订单信息
    * 
    * @access public 
    * @param $order_sn string 订单号
    * @return array 订单信息
    */ 
    public function infoByOrdersn($order_sn)
    {
        $where["order_sn"] = $order_sn;
        return parent::infoBy($where);
    }

    /**   
    * 根据关联订单号来获取订单信息
    * 
    * @access public 
    * @param $target_order_sn string 关联订单号（充值订单和消费订单通过target_order_sn来关联）
    * @return array 订单信息
    */ 
    public function infoByTargetOrdersn($target_order_sn)
    {
        $where["target_order_sn"] = $target_order_sn;
        return parent::infoBy($where);
    }
    
    /**   
    * 修改订单状态
    * 
    * @access public 
    * @param $id int 订单id
    * @param $status int 订单状态
    * @return 返回受影响行数
    */ 
    public function updStatus($id,$status,$msg='')
    {
        $data["id"] = $id;
        $data["status"] = $status;
        $data["msg"] = $msg;
        return parent::upd($data);
    }

    /**   
    * 创建订单号
    * 
    * @access public 
    * @return 返回订单号
    */ 
    public function createOrdersn()
    {
        $order_sn = date("YmdHis").rand(1000,9999);
        if ($this->infoByOrdersn($order_sn)) 
        {
            return $this->createOrdersn();
        }
        return $order_sn;
    }

    /**   
    * 创建订单
    * 
    * @access public 
    * @param $data array 创建订单数据
    * @param $user_info array 用户信息
    * @return 成功返回订单信息，失败返回false
    */ 
    private function createOrders($data,$user_info = null)
    {
        if(!$user_info)
            $user_info = logic("user")->info($data["user_id"]);
        if(!$user_info)
        {
            $this->error = "用户不存在!";
            return false;
        }
        $data["order_sn"] = $this->createOrdersn();
        $data["agent_id"] = $user_info["agent_id"];
        $data["agent_pid"] = $user_info["agent_pid"];
        $data["user_name"] = $user_info["name"];
        $data["reg_date"] = $user_info["reg_date"];
        $data["open_id"] = $user_info["open_id"];
        $data["ip"] = request()->ip();
        $data["add_time"] = $data["pay_time"] = $data["finish_time"] = time();
        $data["pay_date"] = $data["finish_date"] = date("Ymd",$data["add_time"]);
        $res = parent::add($data);
        return $res;
    }

    /**   
    * 创建充值订单
    * 
    * @access public 
    * @param $data array 创建订单数据
    * @return 成功返回充值需要的参数，失败返回false
    */ 
    public function createRechargeOrders($data)
    {
        if(!$data["money"])
        {
            $this->error = "充值金额必须大于0";
            return false;
        }
        $data["type"] = 1;//充值
        $data["pay_type"] = 2;//使用微信或支付宝充值
        $info = $this->createOrders($data);

        if($info)
        {
            /*$pay = new Pay($info["payway_name"]);
            $mydata = $pay->params($info);*/
            $mydata["order_sn"] = $info["order_sn"];
            $mydata["pay_money"] = $info["rmb_money"];
            return $mydata;
        }
        else
        {
            $this->error = "添加订单失败";
            return false;
        }
    }

    
    /**   
    * 创建消费订单
    * 
    * @access public 
    * @param $data array 创建订单数据
    * @return 成功返回订单信息，失败返回false
    */ 
    private function createConsumeOrders($data)
    {
        $data["type"] = 2;//充值
        $data["pay_type"] = 2;//使用微信或支付宝充值

        $info = $this->createOrders($data);
        if($info)
        {
            $goods_list = $data["goods_list"];
            foreach($goods_list as $g)
            {
                $row["order_id"] = $info["id"];
                $row["order_sn"] = $info["order_sn"];
                $row["goods_type_id"] = $g["type_id"];
                $row["goods_type_relate_val"] = $g["type_relate_val"];
                $row["goods_id"] = $g["id"];
                $row["goods_name"] = $g["name"];
                $row["goods_num"] = $g["num"];
                $row["goods_price"] = $g["m_price"];
                $row["use_time_limit"] = $g["use_time_limit"];
                $row["goods_img"] = $g["img"];
                $row["goods_desp"] = $g["desp"];
                $mydata[] = $row;
                
            }
            $res = logic("orders_detail")->adds($mydata);
            if(!$res)
            {   
                return false;
            }
            else
            {
                return $info;
            }
        }
    }

    /**   
    * 根据订单金额，优惠券金额和用户余额，算出用户需要支付的金额以及支付方式（人民币支付/余额支付/混合支付）
    * 
    * @access public 
    * @param $money decimal 订单金额
    * @param $user_money decimal 用户余额
    * @param $coupon_money decimal 优惠券金额
    * @return 
    *            $data[pay_type] //支付方式（1:余额支付,2:人民币支付,3:混合支付）
    *            $data["consume"]["money"] //【消费】订单金额
    *            $data["consume"]["coupon_money"] //【消费】优惠券金额
    *            $data["consume"]["pt_money"] //【消费】平台币金额
    *            $data["consume"]["rmb_money"] //【消费】人民币金额
    *            $data["recharge"]["rmb_money"] //【充值】人民币金额
    *            $data["recharge"]["money"] //【充值】订单金额
    *            $data["recharge"]["coupon_money"] //【充值】优惠券金额
    *            $data["recharge"]["pt_money"] //【充值】平台币金额
    */
    public function loadMoneyInfo($money,$user_money,$coupon_money)
    {
        if($coupon_money>=$money)
        {
            $this->error = "订单金额太小，不能使用优惠券";
            return false;
        }
        $data["consume"]["money"] = $money;
        $data["consume"]["coupon_money"] = $coupon_money;
        if($money-$coupon_money-$user_money<=0)
        {
            $data["pay_type"] = 1;//余额支付
            $data["consume"]["pt_money"] = $money-$coupon_money;
            $data["consume"]["rmb_money"] = 0;
        }
        else
        {
            $data["consume"]["pt_money"] = $user_money;
            $data["consume"]["rmb_money"] =$money-$coupon_money-$user_money;
            if($user_money>0)//混合支付
            {
                $data["pay_type"] = 3;                
            }
            else
            {
                $data["pay_type"] = 2;//人民币支付
            }

            $data["recharge"]["rmb_money"] = $data["consume"]["rmb_money"];
            $data["recharge"]["money"] = $data["recharge"]["rmb_money"];
            $data["recharge"]["coupon_money"] = 0;
            $data["recharge"]["pt_money"] = 0;
        }
        return $data;
    }

    /**   
    * 支付（用户余额够用，则用余额支付，否则不足部分用微信/支付宝支付）
    * 
    * @access public 
    * @param $data array 创建订单数据
    * @return 失败返回false，余额不够用：返回支付需要参数，余额够用：返回true
    */
    public function pay($data)
    {
        $user_info = logic("user")->info($data["user_id"],"","",true);
        if(!$user_info)
        {
            $this->error = "用户不存在";
            return false;
        }
        $data["goods_ids"] = array_column($data["goods_ids_nums"],"id");
        $goods_list = logic("goods")->listByIds($data["goods_ids"]);
        if(!$goods_list)
        {
            $this->error = "商品不存在";
            return false;
        }
        foreach ($goods_list as &$goods) 
        {
            foreach ($data["goods_ids_nums"] as $goods_id_num) 
            {
                if($goods["id"] == $goods_id_num["id"])
                {
                    $money += $goods["m_price"]*$goods_id_num["num"];
                    $goods["num"] = $goods_id_num["num"];
                }
            }
        }
        $data["goods_list"] = $goods_list;
        Db::startTrans();
        $money_data = $this->loadMoneyInfo($money,$user_info["money"],$data["coupon_money"]);
        $data["pay_type"] = $money_data["pay_type"];
        if($data["pay_type"] == 1 )//余额支付
        {
            $data["status"] = 4;
            $data = array_merge($data,$money_data["consume"]);
            $order_info = $this->createConsumeOrders($data);//创建消费订单，并设置订单状态为支付成功
            if($order_info)
            {
                $res = $this->deliveryGoods($order_info);
                if($res)
                {
                    Db::commit();
                    return true;
                }
                else
                {
                    Db::rollback();
                    return false;
                }
            }
            else
            {
                Db::rollback();
                return false;

            }
            
        }
        else
        {
            $data = array_merge($data,$money_data["recharge"]);
            $res = $this->createRechargeOrders($data);//添加充值订单
            if($res)
            {
                $data = array_merge($data,$money_data["consume"]);
                $data["target_order_sn"] = $res["order_sn"];
                $result = $this->createConsumeOrders($data);
                if($result)
                {
                    Db::commit();
                    return $res;    
                }
                else
                {
                    Db::rollback();
                    return false;
                }
            }
            else
            {
                Db::rollback();
                return false;
            }
        }
    }

    /**   
    * 发货
    * 
    * @access public 
    * @param $order_info array 订单信息
    * @return 失败返回false，成功返回true
    */
    public function deliveryGoods($order_info)
    {
        $target_order_info = $this->infoByOrdersn($order_info["target_order_sn"]);
        if($target_order_sn)
        {
            if(!in_array($target_order_info["status"], [4,5]))//状态4：支付成功,5：发货失败，这两种状态下才能发货
            {
                $this->error = "非法操作!";
                return false;
            }
        }
        
        Db::startTrans();
        $user_info = logic("user")->info($order_info["user_id"],"","",true);

        $user_remain_money = $user_info["money"]-($order_info["money"]-$order_info["coupon_money"]);
        if($user_remain_money<0)
        {
            $this->error = "余额不足!";
            Db::rollback();
            return false;
        }
        
        $res = logic("user")->updMoney($user_info["id"],$user_info["money"],$user_remain_money);
        if(!$res)
        {
            $this->error = "扣除用户余额失败";
            Db::rollback();
            return false;
        }
        $res = $this->updStatus($order_info["id"],6);//修改订单状态为已发货
        if(!$res)
        {
            $this->error = "修改订单状态失败";
            Db::rollback();
            return false;
        }
        $orders_detail_list = logic("orders_detail")->listByOrderid($order_info["id"]);
        if($orders_detail_list)
        {
            foreach($orders_detail_list as $orders_detail)
            {
                $ug["user_id"] = $order_info["user_id"];
                $ug["goods_id"] = $orders_detail["goods_id"];
                $ug["goods_name"] = $orders_detail["goods_name"];
                $ug["goods_img"] = $orders_detail["goods_img"];
                $ug["goods_desp"] = $orders_detail["goods_desp"];
                $ug["goods_type_id"] = $orders_detail["goods_type_id"];
                $ug["goods_type_relate_val"] = $orders_detail["goods_type_relate_val"];
                $ug["goods_use_time_limit"] = $orders_detail["use_time_limit"];
                $ug["goods_num"] = $orders_detail["goods_num"];
                $ug["use_start_time"] = time();
                $ug["use_end_time"] = time()+$orders_detail["use_time_limit"];
                $ugs[] = $ug;
            }
            $res = logic("user_goods")->adds($ugs);
            if($res)
            {
                Db::commit();
                return true;
            }
            else
            {
                Db::rollback();
                $this->error = "添加用户商品失败";
                return false;
            }
        }
        else
        {
            Db::commit();
            return true;
        }
    }

    /**   
    * 支付成功回调
    * 
    * @access public 
    * @param $order_sn 订单号
    * @return 失败返回false，成功返回true
    */
    public function notifySucc($order_sn)
    {
        $order_info = $this->infoByOrdersn($order_sn);
        if(!$order_info)
        {
            $this->error = "订单不存在";
            return false;
        }
        if($order_info['type'] != 1)//充值
        {
            $this->error = "订单类型错误";
            return false;
        }
        if(!in_array($order_info["status"], [0,1,3]))//状态0：待支付,1：支付中，3：支付失败
        {
            $this->error = "非法操作";
            return false;
        }
        $res = $this->updStatus($order_info["id"],4);//修改订单状态为已支付
        if($res)
        {
            Db::startTrans();
            $user_info = logic("user")->info($order_info["user_id"],"","",true);
            $user_remain_money = $user_info["money"]+$order_info["money"];
            $res = logic("user")->updMoney($user_info["id"],$user_info["money"],$user_remain_money);
            if($res)
            {
                //如果存在关联的消费订单，则进行发货操作
                $target_order_info = $this->infoByTargetOrdersn($order_sn);
                if($target_order_info)
                {
                    $res = $this->deliveryGoods($target_order_info,$user_info);
                    if($res)
                    {
                        Db::commit();
                        return true;
                    }
                    else
                    {
                        Db::rollback();
                        return false;
                    }
                }
                else
                {
                    Db::commit();
                    return true;
                }
            }
            else
            {
                Db::rollback();
                $this->error = "给用户加钱失败";
                return false;
            }
            //发货
        }
        else
        {
            $this->updStatus($order_info["id"],3);
            $this->error = "修改订单状态失败";
            return false;
        }
    }
    
    /**   
    * 支付失败回调
    * 
    * @access public 
    * @param $order_sn 订单号
    * @return 失败返回false，成功返回true
    */
    public function notifyFail($order_sn)
    {
        $order_info = $this->infoByOrdersn($order_sn);
        if(!$order_info)
        {
            $this->error = "订单不存在";
            return false;
        }
        if($order_info['type'] != 1)//充值
        {
            $this->error = "订单类型错误";
            return false;
        }
        if(!in_array($order_info["status"], [0,1,3]))//状态0：待支付,1：支付中，3：支付失败
        {
            $this->error = "非法操作";
            return false;
        }
        $res = $this->updStatus($order_info["id"],3);//修改订单状态为已支付
        if($res)
        {
            return true;
        }
        else
        {
            $this->error = "修改订单状态失败";
            return false;
        }
    }
}