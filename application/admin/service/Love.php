<?php
namespace app\admin\service;

use think\Model;
use think\Db;
class Love extends BaseService
{


	//素材列表
	public function resourceList($data){
		$res = logic('love_resource')->listBy('','','','sort desc');
		if($res){
			foreach ($res as $key => $value) {
				if($value['share_ico'])
					$res[$key]['share_ico'] = json_decode($value['share_ico'],true);
				if($value['share_title'])
					$res[$key]['share_title'] = explode(',',$value['share_title']);
				unset($res[$key]['add_time']);
				unset($res[$key]['sort']);
				unset($res[$key]['status']);
			}
			return $res;
		}
	}

	//素材详情
	public function resourceInfo($data){
		$info = logic('love_resource')->shareInfo($data);
		unset($info['id']);
		$info['no'] = logic('love_resource')->noWords(['resource_id'=>$data['resource_id']],'sort desc');
		return $info;
	}

	//素材提交
	public function resourceSubmit($data){
		return logic('love_resource')->resourceSubmit($data);
	}

	//获取分享信息
	public function shareInfo($data){
		$info = logic('love_resource')->shareInfo($data);
		$resource_id = model('love_msg')->infoBy(['id'=>$data['share_id']],'resource_id')['resource_id'];
		$info['no'] = logic('love_resource')->noWords(['resource_id'=>$resource_id],'sort desc');
		return $info;
	}

	//获取分享信息
	public function shareSave($data){
		return logic('love_resource')->shareSave($data);
	}
}