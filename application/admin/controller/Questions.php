<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
class Questions extends BaseController
{

	function __construct()
	{
		parent::__construct();
	}

	public function index() {
        $search['title'] = input("param.title",'');
        $search['app_id'] = input("param.app_id",'');  
        $app = logic('app')->listBy();
		$list = logic($this->model)->search($search);
		$this->data["app"] = $app;
		$this->data["list"] = $list;
		$this->data["search"] = $search;
		$this->data["app_opt_status"] = logic($this->model)->opt_status;
		$this->data["count"] = logic($this->model)->search($search,true);
		return view("index", $this->data);
	}

	public function edit() {
		$id = input('param.id', '0');
		$app = logic('app')->listBy();
		$this->data["app"] = $app;
		if ($id) {
			$info = logic($this->model)->info($id);
			$info['options'] = json_decode($info['options'],true);
			$this->data["info"] = $info;	
			$this->data['opt_status'] = logic($this->model)->opt_status;
			$this->data['type'] = 'edit';
		}else{
			$this->data['type'] = 'add';
		}
		return view("edit", $this->data);
	}

	public function upd() {
		$id = input('post.id', '0');
		$data = input();
		$options = $data['options'];
		foreach ($options as $key => $value) {
			if($value['op'] == $data['answer']){
				$options[$key]['is_answer'] = 1;
			}else{
				$options[$key]['is_answer'] = 0;
			}
		}
		$data['options'] = json_encode($options);
		$file = request()->file('ico') ? request()->file('ico') : request()->file('img');
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS .$this->model);
			if($info){
				$data['mp3_name'] = config('http_type').$_SERVER['HTTP_HOST'].'/uploads/'.$this->model.'/'.str_replace('\\', '/', $info->getSaveName()); 
			}else{
		            // 上传失败获取错误信息
				$this->error($file->getError());exit;
			}
			
		}
		// var_dump($data);exit;
		if ($id) {
			$res = logic($this->model)->upd($data);
			if ($res !== false) {
				$this->success('修改成功');
			} else {
				$msg = logic($this->model)->getError();
				$this->error($msg);
			}
		} else {
			// $data['add_time'] = time();
			$res = logic($this->model)->add($data);
			if ($res) {
				$this->success("添加成功", url("index"));
			} else {
				$this->error(logic($this->model)->getError());
			}
		}
	}
}