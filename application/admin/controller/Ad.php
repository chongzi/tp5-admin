<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
class Ad extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }
}