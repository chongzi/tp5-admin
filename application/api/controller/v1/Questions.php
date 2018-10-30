<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use think\Cache;

class Questions extends Base {
		//获取题目列表
	public function getQuestionList(){
		$data = $this->data;
		$res = service("questions")->getQuestionList($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('questions')->getError(),0);
		}	
	}
	//获取题目列表 钱
	public function getQuestionMoney(){
		$data = $this->data;
		$res = service("questions")->getQuestionMoney($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('questions')->getError(),0);
		}	
	}

	//判断答案
	public function getAnswerMoney(){
		$data = $this->data;
		$res = service("questions")->getAnswerMoney($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('questions')->getError(),0);
		}	
	}
}
