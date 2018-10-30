<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class OrdersDetail extends BaseLogic
{
    /*******************************基础配置开始*******************************/
    
    public $flag_opt = array();
    public $id_alias = "orders_detail_id";//主表与其他表关联的字段
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
    * 根据订单id获取对应的商品列表信息 
    * 
    * @access public 
    * @param $order_id int 订单id
    * @return array 商品列表信息
    */
    public function listByOrderid($order_id)
    {
        $where["order_id"] = $order_id;
        return parent::listBy($where);
    }
}