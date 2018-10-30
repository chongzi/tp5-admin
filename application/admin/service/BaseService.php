<?php
namespace app\admin\service;
use Think\Request;
use think\Db;
use think\Model;

class BaseService  extends Model
{
	//验证用户存在
    protected function checkUserId($user_id){
    	$user_info = logic("user")->infoBy(['id'=>$user_id]);
		if(!$user_info)
		{
			$this->error = "用户不存在";
			return false;
		}else{
			return $user_info;
		}
    }

    //验证应用存在
    protected function checkAppId($app_id){
    	$app_info = logic("app")->infoBy(['id'=>$app_id]);

    	if(!$app_info)
		{
			$this->error = "应用不存在";
			return false;
		}else{
			return $app_info;
		}
    }

    //验证礼物存在
    protected function checkGiftId($gift_id){
    	$gift_info = logic("gift")->infoBy(['id'=>$gift_id]);
    	if(!$gift_info)
		{
			$this->error = "礼品不存在";
			return false;
		}else{
			return $gift_info;
		}
    }

    //获取平台金币转换比
    protected function getConvertGiftRate($app_id)
    {
    	$convert_gift_rate = 0;
    	$app_info = logic("app")->info($app_id);
    	if($app_info)
    	{
    		$convert_gift_rate = $app_info["convert_gift_rate"];
    	}
    	return $convert_gift_rate;
    }
}