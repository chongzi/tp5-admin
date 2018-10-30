<?php
namespace app\admin\service;
use Think\Request;
use think\Db;
use think\Model;
use think\Cache;

class User extends BaseService
{
    public function wxLoginOrReg($data)
    {

		if (!$data['code']) {
			$this->error = "code不能为空";
			return false;
		}
		if (!$data['app_id']) {
			$this->error = "应用id不能为空";
			return false;
		}
		$code = $data['code'];
		$app_id = $data['app_id'];

		$wx_app_id = Cache::get('wx_app_id_' . $app_id);
		$secret = Cache::get('wx_app_secret_' . $app_id);
		if (!$wx_app_id || !$sectet) {
			$info = logic('app')->info($app_id);
			if (!$info) {
				$this->error = "应用不存在";
				return false;
			}
			$wx_app_id = $info['wx_app_id'];
			$secret = $info['wx_app_secret'];
			Cache::set('wx_app_id_' . $app_id, $wx_app_id);
			Cache::set('wx_app_secret_' . $app_id, $secret);
		}
		$encryptedData = $data['encryptedData'];
		$iv = $data['iv'];
		$open_info = logic('user')->getWeixinData($code, $encryptedData, $iv, $wx_app_id, $secret);
		if (!$open_info) {
			Cache::rm('wx_app_id_' . $app_id);
			Cache::rm('wx_app_secret_' . $app_id);
			$this->error = "获取用户微信信息失败";
			return false;
		}

		$base_info['open_id'] = $open_info['openId'];
		if($open_info['union_id'])
		{
			$base_info['union_id'] = $open_info['union_id'];
		}
		else
		{
			$base_info['union_id'] = $open_info['openId'];
		}
		$base_info['app_id'] = $app_id;
		$base_info['nick_name'] = $data['nick_name'] ? $data['nick_name'] : ($open_info['nickName'] ? $open_info['nickName'] : '');
		$base_info['face'] = $data['face'] ? $data['face'] : ($open_info['avatarUrl'] ? $open_info['avatarUrl'] : '');

		$user_info = logic('user')->wxLoginOrReg($base_info);
		if ($user_info) {
			$user_info['session_key'] = $open_info['session_key'];
			return $user_info;
		}
		else
		{
			$this->error = logic('user')->getError();
			return false;
		}
    }

    protected function userFormat($user_info) {
		return [
			'id' => $user_info['id'],
			'name' => $user_info['name'],
			'nick_name' => $user_info['nick_name'],
			'face' => $user_info['face'],
		];
	}

	public function info($data)
	{
		$app_id = $data["app_id"];
		$user_id  = $data["user_id"];
		
		$user_info = logic("user")->info($user_id);
		$user_info = $this->userFormat($user_info);

		$where["app_id"] = $app_id ;
		$where["user_id"] = $user_id ;
		$user_app_info = logic("user_app")->infoBy($where);
		if($user_app_info)
		{
			$user_info = array_merge($user_app_info,$user_info);
		}
		return $user_info;
	}

}