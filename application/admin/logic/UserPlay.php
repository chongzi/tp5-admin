<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class UserPlay extends BaseLogic
{
    /*******************************基础配置开始*******************************/
    
    public $flag_opt = array();
    public $id_alias = "user_app_id";//主表与其他表关联的字段
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
    
    public function edit($data)
    {
        if($data["app_id"] && $data["user_id"] )
        {
            $where["app_id"] = $data["app_id"];
            $where["user_id"] = $data["user_id"];
            $info = parent::infoBy($where);
            if($info)
            {
                $mydata["id"]=$info["id"];
                $mydata["last_login_time"]=time();
                return parent::upd($mydata) !== false;
            }
            else
            {
                $data["last_login_time"]=time();
                $data["add_time"]=time();
                return parent::add($data);
            }
        }
        else
        {
            $this->error = "参数错误";
            return false;
        }
    }

    public function phb($app_id,$field_order,$page=1,$page_size=1000,&$count)
    {
        return model("user_app")->phb($app_id,$field_order,$page,$page_size,$count);
    }

}
