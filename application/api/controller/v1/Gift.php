<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use think\Cache;

class Gift extends Base {
	//获取礼品列表
	public function getGiftList()
	{
		$data = $this->data;
		$res = service("gift")->listBy($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('gift')->getError(),0);
		}
	}

	//我的兑换礼品
	public function myGiftList()
	{
		$data = $this->data;
		$res = service("gift_convert_log")->listBy($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('gift')->getError(),0);
		}
	}

	//兑换礼物
	public function changeGift(){
		$data = $this->data;
		$res = service("gift_convert_log")->changeGift($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn(service('gift_convert_log')->getError(),0);
		}
	}
}
