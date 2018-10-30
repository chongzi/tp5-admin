<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
class Platform extends BaseController
{

	function __construct()
	{
		parent::__construct();
	}

	public function index() {
        $search['title'] = input("param.title",'');
		$list = logic($this->model)->search($search);
		$this->data["list"] = $list;
		$this->data["search"] = $search;
		$this->data["app_opt_status"] = logic($this->model)->opt_status;
		return view("index", $this->data);
	}

	public function edit() {
		$id = input('param.id', '0');
		if ($id) {
			$info = logic($this->model)->info($id);
			$this->data["info"] = $info;
			$this->data['opt_status'] = logic($this->model)->opt_status;
		}
		return view("edit", $this->data);
	}

	//修改、添加 带图片
	public function updImg() {
		$file = request()->file('ico') ? request()->file('ico') : request()->file('img');
		$id = input('post.id', '0');
		$data = request()->post();
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS .$this->model);
			if($info){
				$data['ico'] = config('http_type').$_SERVER['HTTP_HOST'].'/uploads/'.$this->model.'/'.str_replace('\\', '/', $info->getSaveName()); 
			}else{
		            // 上传失败获取错误信息
				$this->error($file->getError());exit;
			}
			
		}
		if ($id) {
			$res = logic($this->model)->upd($data,false);
			if ($res !== false) {
				$this->success('修改成功');
			} else {
				$msg = logic($this->model)->getError();
				$this->error($msg);
			}
		}else{
			$data['add_time'] = time();
			$res = logic($this->model)->add($data,false);
			if ($res !== false) {
				$this->success('添加成功');
			} else {
				$msg = logic($this->model)->getError();
				$this->error($msg);
			}
		}
	}

	//平台列表
	public function platformList(){
		$list = logic($this->model)->listBy(['status'=>1],'id,name,ico,click_num,appid','sort desc');
		if($list){
			apkReturn($list);
		}else{
			apkReturn('无数据',0);
		}
		
	}

	//应用点击
	public function appClick(){
		$data = request()->post();
		if($data['id']){
			$result = logic($this->model)->appClick($data['id']);
			if($result)
				apkReturn([]);
		}else{
			apkReturn('无应用id',0);
		}
	}

	public function test(){
		$url = "https://www.qqtn.com/exe/sajax.asp?action=2&sdate=20180624&edate=20180626&p=1&num=50";
		$a = returnApi($url);
		var_dump($a);exit;
	}
	
}