<?php
namespace app\admin\service;

use think\Model;
use think\Db;
class Wxgame extends BaseService
{
    

    //获取礼品列表
	public function getUserInfo($data)
	{
		$app_id = $data["app_id"];
		if(!$this->checkAppId($app_id)){	//验证应用id
			return false;exit;
		}

		$res = logic("wxgame")->getUserInfo($data);
		if($res){
			return $res;
		}else{
			$this->error = "获取数据失败";
			return false;
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
}