<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
use think\Request;
class MoreApp extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }


    public function index(Request $Request) {
        $search['title'] = input("param.title",'');  
        $list = logic($this->model)->search($search);
        $this->data["list"] = $list;
        $this->data["search"] = $search;
        return view("index", $this->data);
    }

    public function edit() {
        $id = input('param.id', '0');
        if ($id) {
            $info = logic($this->model)->info($id);
            $this->data["info"] = $info;
        }
        return view("edit", $this->data);
    }

}