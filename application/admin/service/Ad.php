<?php
namespace app\admin\service;
use Think\Request;
use think\Db;
use think\Model;
use think\Cache;

class Ad extends BaseService
{
    public function info($data)	
	{
		$info = logic("ad")->info($data["id"]);
		return $info;
	}

	public function listBy($data)	
	{
		$info = logic("ad")->listBy($where);
		return $info;
	}
}