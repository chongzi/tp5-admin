<?php
namespace app\api\controller\v1;
use think\Controller;
class Platform extends Controller
{


	//平台列表
	public function platformList(){
		$data = request()->post();
		if($data['app_id']){
			$result = logic('app')->listBy(['id'=>$data['app_id']])[0];
			$arr = logic('app')->viewInfoBy($result['more_app_id']);
			// var_dump($arr);exit;
			if($arr){
				apkReturn($arr);
			}else{
				apkReturn('无数据',0);
			}
		}else{
			apkReturn('无app_id',0);
		}
	
	}

	//应用点击
	public function appClick(){
		$data = request()->post();
		if($data['id']){
			$result = logic('more_app')->appClick($data['id']);
			if($result)
				apkReturn([]);
		}else{
			apkReturn('无id',0);
		}
	}

	//获取分享信息
	public function shareInfo(){
		$data = request()->post();
		if($data['app_id']){
			$result = logic('app')->listBy(['id'=>$data['app_id']])[0];
			if($result){
				$info['title'] = explode(',',$result['share_title']);
				$info['ico'] = json_decode($result['share_ico'],true);
				$key = array_rand($info['title']);
				$i[0]['title'] = $info['title'][$key];
				$i[0]['ico'] = $info['ico'][$key];
				apkReturn($i);
			}else{
				apkReturn('无数据',0);
			}
		}else{
			apkReturn('无app_id',0);
		}		
	}
	

	function water(){
		$src = 'uploads/Gift/20180530/1d4dc51b0a572cd3a16a4e554c04b537.jpg';
		//指定图片路径
		// $src = '001.png';
		//获取图片信息
		$info = getimagesize($src);
		//获取图片扩展名
		$type = image_type_to_extension($info[2],false);
		//动态的把图片导入内存中
		$fun =  "imagecreatefrom{$type}";
		$image = $fun($src);
		var_dump('Content-type:'.$info['mime']);exit;
		//指定字体颜色
		$col = imagecolorallocatealpha($image,255,255,255,50);
		//指定字体内容
		$content = 'helloworld';
		//给图片添加文字
		imagestring($image,5,20,30,$content,$col);
		//指定输入类型
		header('Content-type:'.$info['mime']);
		//动态的输出图片到浏览器中
		$func = "image{$type}";
		$func($image);
		//销毁图片
		imagedestroy($image);
	}
}