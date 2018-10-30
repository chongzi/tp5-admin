<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class Wxgame extends BaseLogic
{
    /*******************************基础配置开始*******************************/
    
    public $flag_opt = array();
    public $id_alias = "";//主表与其他表关联的字段
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

    public function getUserInfo($data){
        return model('wxgame')->getUserInfo($data);
    }
}