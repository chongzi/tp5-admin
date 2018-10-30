<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use think\Cache;

class User extends Base {

	public function reg() 
	{
		if ($this->data['app_type'] == "app") 
		{
			$this->appReg();
		} 
		else if ($this->data['app_type'] == "wx") 
		{
			$this->wxLoginOrReg();
		} 
		else 
		{
			return $this->apkReturn('参数错误', 0);
		}
	}

	public function login() 
	{
		if ($this->data['app_type'] == "app") 
		{
			$this->appLogin();
		} 
		else if ($this->data['app_type'] == "wx") 
		{
			$this->wxLoginOrReg();
		} 
		else 
		{
			return $this->apkReturn('参数错误', 0);
		}
	}

	/**
	 * app注册
	 * @input    user_name
	 *           password
	 *           code
	 */
	private function appReg() {
		
	}

	/**
	 * app登录
	 * @input    mobile
	 *           password
	 */
	private function appLogin() {
		
	}

	private function wxLoginOrReg() 
	{
		$data = $this->data;
		$data["name"] = $this->setDefVal($data,"name","","test");
		$res = service("user")->wxLoginOrReg($data);
		if($res)
		{
			if($this->isJwt()) 
			{
				$res["token"] = $this->getToken($res['id']);
			}

			return $this->apkReturn($res);
		}
		return $this->apkReturn(service('user')->getError(), 0);
	}

	public function info()
	{
		$data = $this->data;
		$res = service("user")->info($data["user_id"]);
		return $this->apkReturn($res);
	}
}
