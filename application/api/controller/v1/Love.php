<?php
namespace app\api\controller\v1;
use think\Controller;
// use app\api\controller\Base;
use think\Cache;
class Love extends Controller
{


	//素材列表
	public function resourceList(){
		$data = request()->post();
		$result = service('love')->resourceList($data);
		if($result){
			return apkReturn($result);
		}else{
			return apkReturn(logic('love_resource')->getError(),0);
		}
	
	}

	//素材列表
	public function resourceInfo(){
		$data = request()->post();
		$result = service('love')->resourceInfo($data);
		if($result){
			return apkReturn($result);
		}else{
			return apkReturn(logic('love_resource')->getError(),0);
		}
	
	}


	//素材提交
	public function resourceSubmit(){
		$data = request()->post();
		if($data['resource_id']){
			if($data['content'] && $data['left_button'] && $data['left_button'] && $data['desc']){
				$result = service('love')->resourceSubmit($data);
				if($result){
					return apkReturn($result);
				}
			}else{
				return apkReturn('提交信息不完整',0);
			}
			
		}else{
			return apkReturn('无素材id',0);
		}
		
	}

	//获取分享信息
	public function shareInfo(){
		$data = request()->post();
		if($data['app_id']){
			if($data['share_id']){
				$result = service('love')->shareInfo($data);
				if($result){
					return apkReturn($result);	
				}else{
					return apkReturn(logic('love_resource')->getError(),0);
				}
			}else{
				return apkReturn('无分享id',0);
			}
			
		}else{
			return apkReturn('无app_id',0);
		}
	}


	//分享记录
	public function shareSave(){
		$data = request()->post();
		if($data['app_id']){
			$result = service('love')->shareSave($data);
			if($result){
				return apkReturn($result);		
			}else{
				return apkReturn(logic('love_resource')->getError(),0);
			}
		}else{
			return apkReturn('无app_id',0);
		}
	}





	// //获取分享信息
	// public function shareInfo(){
	// 	$data = request()->post();
	// 	if($data['app_id']){
	// 		$result = logic('app')->listBy(['id'=>$data['app_id']])[0];
	// 		if($result){
	// 			$info['title'] = explode(',',$result['share_title']);
	// 			$info['ico'] = json_decode($result['share_ico'],true);
	// 			$key = array_rand($info['title']);
	// 			$i[0]['title'] = $info['title'][$key];
	// 			$i[0]['ico'] = $info['ico'][$key];
	// 			apkReturn($i);
	// 		}else{
	// 			apkReturn('无数据',0);
	// 		}
	// 	}else{
	// 		apkReturn('无app_id',0);
	// 	}
		
				
	// }
	
}