<?php

namespace app\api\controller\v1;

// use app\api\controller\Base;
use think\Cache;
use think\Controller;
use think\Request;
class Wxgame extends Controller {

	//储存微信小游戏用户信息
	public function getUserInfo(Request $request){
		$data = $request->post();
		$res = service("wxgame")->getUserInfo($data);
		if($res){
			return apkReturn($res);
		}else{
			return apkReturn(service('wxgame')->getError(),0);
		}
	}
	
	//提交分数
	public function postScore(Request $request)
	{
		$data = $request->post();
		$data['add_time'] = time();
		$res = service("user_app")->postScore($data);
		if($res){
			return apkReturn($res);
		}else{
			return apkReturn(service('user_app')->getError(),0);
		}
	}
}
