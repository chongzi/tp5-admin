<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class GoodsType extends BaseLogic
{
    /*******************************基础配置开始*******************************/
    
    public $flag_opt = array();
    public $id_alias = "goods_type_id";//主表与其他表关联的字段
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
    * 根据分类名成获取分类信息
    * 
    * @access public 
    * @param $name string 分类名
    * @return array 分类信息
    */
    public function infoByName($name)
    {
        $where["name"] = $name;
        return parent::infoBy($where);
    }
 
    /**   
    * 根据id来获取拥有者列表，如[
    *                                 ['id'=>1,'name'=>'svip','own'=>',3,4,5,'],
    *                                 ['id'=>3,'name'=>'vip','own'=>',3,4,'],
    *                                 ['id'=>3,'name'=>'diandu','own'=>'0'],
    *                                 ['id'=>4,'name'=>'weike','own'=>'0'],
    *                                 ['id'=>5,'name'=>'tiku','own'=>'0'],
    *                            ]
    * 
    * @access public 
    * @param $id int 分类id
    * @return array 拥有者列表
    */
    public function ownerList($id)   
    {
        $res = model("goods_type")->ownerList($id);
        return $res;
    }

    /**   
    * 根据名称来获取拥有者列表，如[
    *                                 ['id'=>1,'name'=>'svip','own'=>',3,4,5,'],
    *                                 ['id'=>3,'name'=>'vip','own'=>',3,4,'],
    *                                 ['id'=>3,'name'=>'diandu','own'=>'0'],
    *                                 ['id'=>4,'name'=>'weike','own'=>'0'],
    *                                 ['id'=>5,'name'=>'tiku','own'=>'0'],
    *                            ]
    * 
    * @access public 
    * @param $id int 分类id
    * @return array 拥有者列表
    */
    public function ownerListByName($name)   
    {
        $info = $this->infoByName($name);
        if(!$info)
        {
            return false;
        }
        $res = model("goods_type")->ownerList($info["id"]);
        return $res;
    }
}