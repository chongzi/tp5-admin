<?php

namespace app\api\controller\v1;

use think\Db;
use think\Controller;
class Sendmsg extends Controller {
	//发送模板消息
	function sendMsg(){
		$res = [];
		$list = logic('user_app')->getNoSignUser();
		if(!empty($list)){
			foreach ($list as $key => $value) {
				$sql = 'select id from s_user_sign_in where status = 1 and user_id = '.$value['user_id'].' and app_id = '.$value['app_id'];
				$user_info = logic('user_app')->infoBy(['user_id'=>$value['user_id'],'app_id'=>$value['app_id']],'app_id,open_id,form_id');
				$app_info = logic('app')->infoBy(['id'=>$user_info['app_id']],'title,template_id,template_msg');
				if($app_info['template_id'] == false || $app_info['template_id'] == 'the formId is a mock one' || $app_info['template_id'] == 'undefined'){
					continue;
				}
				$sign_info = model('app')->infoBy(['id'=>$value['app_id']],'sign_arr');
        		$sign_arr = json_decode($sign_info['sign_arr'],true);
				$data['uid'] = $value['user_id'];
				$data['app_id'] = $value['app_id'];
				$data['num'] = count(Db::query($sql));
				$data['templateId'] = $app_info['template_id'];
		        $data['openid'] = $user_info['open_id'];
		        $data['page'] = 'pages/index/index';
		        $data['form_id'] = $user_info['form_id'];
		        $data['name'] = $app_info['template_msg'];
		        $dongxi = $sign_arr[$data['num']];
		        if($dongxi['sign_type'] == 'money'){
		        	$data['wawa'] = '继续签到赠送金币'.$dongxi['sign_value'].'个';
		        }else if($dongxi['sign_type'] == 'played_num'){
		        	$data['wawa'] = '继续签到赠送挑战次数'.$dongxi['sign_value'].'次';
		        }
		        $data['time'] = date('Y-m-d H:i:s');
		        $result = sendMoban($data);
		        if($result['errcode'] == 0 && $result['errmsg'] == 'ok'){
		            $info['user_id'] = $value['user_id'];
		            $info['send_time'] = time();
		            $info['app_id'] = $value['app_id'];
		            $info['send_gift'] = $data['wawa'];
		            $info['sign_num'] = $data['num'];
		            logic('user_app')->updBy(['is_send'=>1],['user_id'=>$value['user_id'],'app_id'=>$value['app_id']]);
		            Db::table('s_template_msg')->insert($info);
		            $res['data'][] = $result;
		        }else{
		            logic('user_app')->updBy(['is_send'=>1],['user_id'=>$value['user_id'],'app_id'=>$value['app_id']]);
		        }
			}
		}
		echo json_encode($res);
	}
}
