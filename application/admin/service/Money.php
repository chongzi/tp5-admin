<?php
namespace app\admin\service;

use think\Model;
use think\Db;
class Money extends BaseService
{
    
	public function changeMoney($data){
		$app_info = $this->checkAppId($data["app_id"]);
		$user_info = $this->checkUserId($data["user_id"]);
		if(!$app_info){
			return false;exit;
		}
		if(!$user_info){
			return false;exit;
		}
		if(is_float($data['money']) || is_numeric($data['money'])){
			$res = logic('order_change')->changeMoney($data);
			if($res['msg'] == 'success'){
				return $res['data'];
			}else{
				$this->error = $res['msg'];
				return false;exit;
			}
		}else{
			$this->error = "money参数类型错误";
			return false;exit;
		}	
		
	}
    //获取礼品列表
	public function listBy($data)
	{
		$app_id = $data["app_id"];
		$field_order = $data["field_order"];//排序字段
		$page = $data["page"];
		$page_size = $data["page_size"];
		if(!$this->checkAppId($app_id)){	//验证应用id
			return false;exit;
		}
		//应用兑换礼品的比例，每个应用兑换的比例可能不一样
		$convert_gift_rate = $this->getConvertGiftRate($app_id);
		$where["app_id"] = $app_id;
		$where['status'] = 1;
		$res = logic("gift")->viewListBy($where,'id,desp,img,name,price');

		foreach ($res as &$row) {
			$row["money"] = $row["price"]*$convert_gift_rate;
		}
		if($res){
			return $res;
		}else{
			$this->error = "获取数据失败";
			return false;
		}
	}


}