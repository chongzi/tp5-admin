<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Loader;

class AliOss extends Controller {

	//获取h5上传的临时key 可以通过临时key上传文件到oss中
	public function get() {
		Loader::import('aliyunOss.h5upload', EXTEND_PATH);
		$h5upload = new \aliyunOss\h5upload;
		$response = $h5upload->get();
		echo json_encode($response);
	}

	//上传成功oss  h5回调的url 此处可以写业务逻辑 进行入库操作  返回的json数据 web页面可以接收到
	public function web() {
		$url = 'http://k1u-xh.oss-cn-shenzhen.aliyuncs.com/' . $_POST['filename'];
		$size = $_POST['size'];
		$mimeType = $_POST['mimeType'];
		$backdata = array(
			'size' => $size,
			'url' => $url,
			'mimeType' => $mimeType,
		);
		$data = array('Status' => '200', 'msg' => 'success', 'data' => $backdata);
		echo json_encode($data);
	}

	//获取app上传的临时key 可以通过临时key上传文件到oss中
	public function osstmpkey() {
		Loader::import('aliyunOss.appupload', EXTEND_PATH);
		$appupload = new \aliyunOss\appupload;
		$response = $appupload->oss_tmp_key();
		echo json_encode($response);
	}

	//上传成功oss  app回调的url 此处可以写业务逻辑 进行入库操作  返回的json数据 app可以接收到
	public function osscallback() {
		Loader::import('aliyunOss.appupload', EXTEND_PATH);
		$appupload = new \aliyunOss\appupload;
		$bloon = $appupload->osscallback();
		if ($bloon) {
			$data = $appupload->data;
			//回调操作 可以返回上传的文件url
			header('Content-Type: application/json');
			$data = array('Status' => '1', 'msg' => 'success', 'data' => array());
			echo json_encode($data);
		} else {
			$data = array('Status' => '0');
			header('http/1.1 403 Forbidden');
			exit();
		}
	}

	//ossfilemanage
	public function manage() {
		Loader::import('aliyunOss.filemanage', EXTEND_PATH);
		$file = new \aliyunOss\filemanage;
		$files = $file->listAllObjects();
		dump($files);
	}

	public function has() {
		Loader::import('aliyunOss.filemanage', EXTEND_PATH);
		$file = new \aliyunOss\filemanage;
		$sql = "select * from s_book2 where answer_imgs REGEXP '^https://anspic.bshu.com/answers/[0-9]{6}/'";
		$result = Db::query($sql);
		foreach ($result as $key => $value) {
			$answer_imgs = explode(',', $value['answer_imgs']);
			$img = $answer_imgs[0];
			$obj = 'answers' . substr($img, strlen('https://anspic.bshu.com/answers/174394'));
			$bloon = $file->doesObjectExist($obj);
			if (!$bloon) {
				echo $img;
				echo '<br/>';
				echo $value['id'];
				echo "<br/>";
			}

		}
	}

}
