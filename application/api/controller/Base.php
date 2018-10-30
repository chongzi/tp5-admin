<?php

namespace app\api\controller;

use think\Log;
use think\Request;

class Base {
	// 去掉注释则开启接口RSA加密或JWT认证。注意只需要开启一个
	// use Crypt;
	use Jwt;

	protected $request;
	protected $data;

	protected $debug = false;
	protected $no_params = ['user_id'];	//接受参数剔除值，一般为 user_id
	protected $private_key_file;
	protected $public_key_file;

	public function __construct() {
		//$this->jwtData();exit()
		$this->request = Request::instance();
		$this->private_key_file = APP_PATH . 'api' . DS . 'config' . DS . 'rsa_private_key.pem';
		$this->public_key_file = APP_PATH . 'api' . DS . 'config' . DS . 'rsa_public_key.pem';

		if ($this->request->get('is_debug') && $this->isDebugIp()) {
			$this->debug = true;
			// 接口不加密
			$this->isCrypt = false;
		}
		$this->getData();
	}

	/**
	 * 检查IP是否有调试权限
	 * @DateTime 2018-03-02T11:46:45+0800
	 * @return   boolean
	 */
	protected function isDebugIp() {
		$ip = $this->request->ip();
		$debug_ip_list = config('debug_ip_list');
		$debug_ip_list = explode(',', $debug_ip_list);
		return in_array($ip, $debug_ip_list);
	}

	/**
	 * 判断接口是否启用JWT认证
	 * @DateTime 2018-03-02T11:47:32+0800
	 * @return   boolean
	 */
	protected function isJwt() {
		return isset($this->isJwt) && $this->isJwt;
	}

	/**
	 * 判断接口是否开启RSA加密
	 * @DateTime 2018-03-02T11:47:59+0800
	 * @return   boolean
	 */
	protected function isCrypt() {
		return isset($this->isCrypt) && $this->isCrypt;
	}

	/**
	 * 获取请求数据
	 * @DateTime 2018-03-02T11:48:24+0800
	 * @return   void
	 */
	protected function getData() {
		$is_public = $this->request->param('is_public');
		if ($is_public) //不采用RSA加密，也不采用JWT
		{
			$this->data = $this->request->param();
		} 
		else 
		{
			if ($this->isCrypt()) {
				$this->rsaData();

			} else if ($this->isJwt()) {
				$this->jwtData();

			} else {
				$this->data = $this->request->param();
			}
			$this->check();
		}

		
		$this->data['app_type'] = input("app_type");
		$this->data['app_id'] = input("app_id");
		$this->data['ip'] = $this->request->ip();

		//过滤空参数
		$this->dataFilter();
	}

	protected function check()
	{
		if($is_public == false){
			$dispatch = $this->request->dispatch();
			$action = $dispatch['module']['2'];
			if (!in_array($action, $this->allowActions)) {
				$keys = array_keys($this->data);
				if($keys != Null){
					sort($keys);
					$str = '';
					foreach ($keys as $key) {
						if (!in_array($key, $this->no_params)) {
							$str .= $key . '=' . $this->data[$key] . '&';
						}
					}
					$str = substr($str, 0, strlen($str) - 1);
					$check = md5(hash("sha256", $str));
					$tk = $_SERVER['HTTP_TK'];
					if ($tk != $check) {
						$response = ['code' => '-101', 'msg' => '验证不通过'];
						$this->apkReturn($response);exit;
					}
				}	
			}
			
		}
	}

	/**
	 * 过滤空参数
	 * @DateTime 2018-03-07T10:49:39+0800
	 * @return   void
	 */
	protected function dataFilter() {
		foreach ($this->data as $key => $value) {
			if ($value === '' || $value === false) {
				unset($this->data[$key]);
			}
		}
	}

	protected function apkReturn($data, $code = 1, $msg = '') {
		$return = [
			'code' => $code,
			'msg' => $msg,
			'data' => '',
		];
		if ($code == 1) {
			$return['data'] = $data;
			$return['msg'] = $msg;
		} else {
			$return['msg'] = $data;
		}
		$this->log('returnData:' . json_encode($return));
		if ($this->debug || !isset($this->isCrypt)) {
			if ($this->debug) {
				dump($return);
			} else {
				echo json_encode($return);
			}
		} else {
			$return = json_encode($return);
			echo $this->compression($return);
		}
		exit;
	}

	protected function log($data) {
		$file = Log::write($data, 'debug');
	}

	protected function setDefVal($data, $name, $def_val, $debug_val) {
		if ($this->debug) {
			return isset($data[$name]) ? $data[$name] : $debug_val;
		} else {
			return isset($data[$name]) ? $data[$name] : $def_val;
		}
	}
}
