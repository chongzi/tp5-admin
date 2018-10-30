<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use think\Cache;

class Game extends Base {

	//提交分数
	public function postScore()
	{
		$data = $this->data;
		$res = service("user_app")->postScore($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('user_app')->getError(),0);
		}
	}

	//获取用户信息
	public function getUserInfo(){
		$data = $this->data;
		$res = service("user_app")->getUserInfo($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('user_app')->getError(),0);
		}
	}

	//排行
	public function topList(){
		$data = $this->data;
		$res = service("user_app")->topList($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('user_app')->getError(),0);
		}	
	}


	//获取应用信息接口
	public function getAppInfo(){
		$data = $this->data;
		$res = service("app")->getAppInfo($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('app')->getError(),0);
		}	
	}


	//投诉接口
	public function tousu(){
		$data = $this->data;

		$res = service("app")->tousu($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('app')->getError(),0);
		}	
	}
	
	//分享
	public function share(){
		$data = $this->data;
		$res = service("app")->share($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('app')->getError(),0);
		}	
	}


	//签到
	public function userSignIn(){
		$data = $this->data;
		$res = service("user_app")->userSignIn($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(logic('user_app')->getError(),0);
		}	
	}

	//用户签到详情
	public function userSignInfo(){
		$data = $this->data;
		$res = service("user_app")->userSignInfo($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(logic('user_app')->getError(),0);
		}	
	}


	//分享
	public function getShareQunInfo(){
		$data = $this->data;
		$res = service("app")->getShareQunInfo($data);
		if($res){
			return $this->apkReturn($res,1,logic('share_info')->getError());
		}else{
			return $this->apkReturn(logic('share_info')->getError(),0);
		}	

	}
}

