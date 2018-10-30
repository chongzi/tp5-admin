<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class Questions extends BaseLogic
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


    //处理题目答案
    public function handleAnswers($data,$num = 1){
        if($data){
            if($num > 0){
                $b = ['a','b','c'];
                $c = mt_rand(0,1);
                if($c == 0){
                    $e = 1;
                }else{
                    $e = 0;
                }
                $d = $b[$c];
                $f = $b[$e];
                $k = -1;
                foreach ($data as $key => $value) {
                    if($value['name']){
                        if($value['is_answer'] == 1){
                            $value['op'] = $b[$c];
                            $a[$c] = $value;
                            continue;
                        }
                        if($value['is_answer'] == 0){
                            if($k == -1){
                                $value['op'] = $b[$e]; 
                                $a[$e] = $value;
                                $k = $key; 
                                continue;  
                            }
                            
                        }
                        if($num == 2){
                            if($value['is_answer'] == 0){
                                $value['op'] = $b[$num]; 
                                $a[$num] = $value;
                            }
                        }

                    }else{
                        unset($data[$key]);
                    }  
                }
            }else{
                $a = $data;
            }
            
        }
        return $a;
    }

    //题目数据去重
    public function uniqueQuestion($questions){
        $b = [];
        if(!empty($questions)){                      //放入一个数组去重
            foreach ($questions as $key => $value) {
                $a = json_decode($value['questions'],true);
                foreach ($a as $k => $v) {
                    array_push($b, $v);
                }
            }
            $b = array_unique($b);  
        }
        return $b;
    }
}