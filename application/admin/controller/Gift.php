<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
class Gift extends BaseController
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
}