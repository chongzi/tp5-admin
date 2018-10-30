<?php
namespace app\admin\service;

use think\Model;
use think\Db;
class GiftConvertLog extends BaseService
{
	//我的礼物
	public function listBy($data)
	{
		$app_id = $data["app_id"];
		$user_id  = $data["user_id"];
		if(!$this->checkAppId($app_id)){
			return false;exit;
		}
		if(!$this->checkUserId($user_id)){
			return false;exit;
		}
		$where["user_id"]  = $user_id;
		$where["app_id"]  = $app_id;
		$res = logic("gift_convert_log")->listBy($where,'gift_id,gift_name,gift_img,money,status');
		return $res;
	}

	//兑换礼品
	public function changeGift($data){
		$app_id = $data["app_id"];
		$user_id  = $data["user_id"];
		$gift_id = $data['gift_id'];
		$app_info = $this->checkAppId($app_id);
		$user_info = $this->checkUserId($user_id);
		$gift_info = $this->checkGiftId($gift_id);
		// if(!$data['name']){
		// 	$this->error = '姓名不能为空';
		// 	return false;exit;
		// }
		// if(!preg_match('/^1[3,4,5,6,7,8]\d{9}$/',$data['phone'])){
		// 	$this->error = '手机号错误';
		// 	return false;exit;
		// }
		// if(!$data['addr']){
		// 	$this->error = '收货地址不能为空';
		// 	return false;exit;
		// }
		
		if($app_info && $user_info && $gift_info){
			$convert_gift_rate = $this->getConvertGiftRate($app_id);

			if(in_array($gift_id, explode(',',$app_info['app_gift_id']))){
				$money = $gift_info['price'] * $convert_gift_rate;
				$user_app_info = logic("user_app")->infoBy(['user_id'=>$user_id,'app_id'=>$app_id]);
				if($app_info['allow_change_money'] <= $user_app_info['money']){
					if($user_app_info['money'] >= $money){
						$res = logic('gift_convert_log')->changeGift($user_app_info,$gift_info,$money,$data);
						if($res){
							return ['app_id'=>$app_id,'gift_id'=>$gift_id];
						}else{
							$this->error = '兑换失败';
							return false;exit;
						}
					}else{
						$this->error = '金币不足，无法兑换';
						return false;exit;
					}
				}else{
					$this->error = '金币数多于'.$app_info['allow_change_money'].'个才能兑换奖品';
					return false;exit;
				}		
			}else{
				$this->error = '该应用未绑定该礼物';
				return false;exit;
			}

		}else{
			$this->error = '缺少核心参数';
			return false;exit;
		}
		
		
	}
}