<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class UserApp extends BaseLogic
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
            $app_info = logic("app")->infoBy(['id'=>$data['app_id']],'user_play_num');
            
            if($info)
            {
            	//是否每日送挑战次数
            	if($app_info['user_play_num'] != 0){
            		$app_user_info = logic("user_app")->infoBy(['app_id'=>$data['app_id'],'user_id'=>$data['user_id']],'playable_num,upd_time');
            		$today_date = date('Ymd',$app_user_info['upd_time']);
            		if($today_date != date('Ymd')){
            			$mydata['playable_num'] = $app_user_info['playable_num'] + $app_info['user_play_num'];
            		}	
            	}
            	
                $mydata["id"]=$info["id"];
                $mydata["upd_time"]=time();
                return parent::upd($mydata) !== false;
            }
            else
            {
            	
                $data["upd_time"]=time();
                $data["add_time"]=time();
                $data['playable_num'] = $app_info['user_play_num'];
                return parent::add($data);
            }
        }
        else
        {
            $this->error = "参数错误";
            return false;
        }
    }


    public function getUserInfo($where){
        $result = model("user_app")->getUserInfo($where);
        $result['total_num'] = (int)model("user_app")->getTotalNum(['app_id'=>$where['app_id']]);
        return $result;
    }

    public function viewListBy($where,$data){
        return model("user_app")->viewListBy($where,$data);
    }

    //签到
    public function userSignIn($data){
        $user_id = $data['user_id'];
        $app_id = $data['app_id'];
        $sign_list = model('user_app')->sign_list($app_id,$user_id);
        $app_info = model('app')->infoBy(['id'=>$app_id],'sign_arr');
        $sign_arr = json_decode($app_info['sign_arr'],true);
        $user_app_info = model('user_app')->infoBy(['user_id'=>$user_id,'app_id'=>$app_id],'playable_num,money');
        $playable_num = $user_app_info['playable_num'];
        $sign_count = $this->getSignday($sign_list);
        if($sign_count < count($sign_arr)){
            $sql_today = "select id from s_user_sign_in where user_id = ".$user_id." and app_id = ".$app_id." and FROM_UNIXTIME(add_time,'%Y%m%d') = curdate()";
            $result = Db::execute($sql_today);
            if($result == null){
                $cur_info = $sign_arr[$sign_count];
                if($cur_info['sign_type'] == 'money'){
                    $sql = 'update s_user_app set money = money + '.(int)$cur_info['sign_value'].' where app_id = '.$app_id.' and user_id = '.$user_id;
                    $f['value'] = $user_app_info['money'] + (int)$cur_info['sign_value'];
                    $f['name'] = 'money';
                    $gift = '金币'.(int)$cur_info['sign_value'].'枚';
                }else if($cur_info['sign_type'] == 'played_num'){
                    $sql = 'update s_user_app set playable_num = playable_num + '.(int)$cur_info['sign_value'].' where app_id = '.$app_id.' and user_id = '.$user_id;
                    $f['value'] = $playable_num + (int)$cur_info['sign_value'];
                    $f['name'] = 'playable_num';
                    $gift = '挑战次数'.(int)$cur_info['sign_value'].'次';
                }
                $res = Db::execute($sql);
                if($res){
                	if($sign_count == count($sign_arr) - 1){
                    	$info['status'] = 0;
	                	model('user_app')->emptySign($app_id,$user_id);
	                }else{
	                	$info['status'] = 1;
	                }
                    $info['user_id'] = $user_id;
                    $info['app_id'] = $app_id;
                    $info['add_time'] = time();
                    $info['add_date'] = date('Y-m-d');
                    $info['ip'] = $data['ip'];  
                    $info['sign_num'] = $sign_count + 1;
                    $info['sign_gift'] = $gift;               
                    Db::table('s_user_sign_in')->insert($info);
                    return ['sign_count'=>$sign_count + 1,'user_info'=>$f];
                }
            }else{
                $this->error = "你今天已签到过,明天再来";
                return false;
            }
            
        }else{
        	model('user_app')->emptySign($app_id,$user_id);
            $this->error = "签到次数清零";
            return false;
        }
    }

    //用户签到列表
    public function userSignInfo($data){
    	$user_id = $data['user_id'];
        $app_id = $data['app_id'];
        $sign_list = model('user_app')->sign_list($app_id,$user_id);
        $info['sign_count'] = $this->getSignday($sign_list);
        $app_info = model('app')->infoBy(['id'=>$app_id]);
        $sign_arr = json_decode($app_info['sign_arr'],true);
        if($sign_arr){
        	foreach ($sign_arr as $key => $value) {
	        	if($value['sign_type'] == 'money'){
	        		$info['list'][$key] = '兑换金币'.$value['sign_value'].'枚';
	        	}else if($value['sign_type'] == 'played_num'){
	        		$info['list'][$key] = $value['sign_value'].'次';
	        	}
	        }
        }
        
        return $info;
    }

    //返回连续签到天数
    public function getSignday($sign_list){
    	if($sign_list){
        	$last_sign_date = $sign_list[count($sign_list)-1]['add_date'];
	       	$time = (strtotime(date('Y-m-d')) - strtotime($last_sign_date))/3600;
	        if($time == 24 || $time === 0){
	        	$sign_count = count($sign_list);
	        }else{
	        	$sign_count = 0;
	        }
        }else{
        	$sign_count = 0;
        }
        return $sign_count;
    }

    //获取连续签到用户
    public function getNoSignUser(){
        $sql = "select user_id,app_id from s_user_sign_in where add_time>UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY))";
        return Db::query($sql);
    }

    //获取用户签到详情
    public function getSignInfo($data){
        $sign_list = model('user_app')->sign_list($data['app_id'],$data['user_id']);
        return $sign_list;
    }

    //获取用户模板消息详情
    public function getTemplateInfo($data){
        $template_list = model('user_app')->template_msg_list($data);
        return $template_list;
    }
}
