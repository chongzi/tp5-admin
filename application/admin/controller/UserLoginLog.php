<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
class UserLoginLog extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

 	public function index()
    {
        $page = input("param.p",1) ;
        $search["stype"] = input("get.stype",2) ;
        $search["sval"] = trim(input("get.sval")) ;
        if($search["stype"] && $search["sval"])
        {
            if($search["stype"] == 1)
                $search["id"] = $search["sval"];
            else if($search["stype"] == 2)
                $search["name"] = $search["sval"];
            else if($search["stype"] == 3)
                $search["mobile"] = $search["sval"];
        }
        $search['start_time'] = input("get.start_time");
        $search['end_time'] = input("get.end_time");
        $page_size =10;
        $list = logic($this->model)->search($search,"id desc",$page,$page_size,$count) ;
        $page_html = get_page_html($count,$page_size);
        $this->data["list"] = $list;
        $this->data["page_html"] = $page_html;
        $this->data['search'] = $search;
        return view("index", $this->data);
    }
}