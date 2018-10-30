<?php
namespace app\admin\service;

use think\Model;
use think\Db;
class Gift extends BaseService
{
    

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