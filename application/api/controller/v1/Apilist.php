<?php

namespace app\api\controller\v1;
use think\Controller;

class Apilist extends Controller {

	public function index(){
		return view('apilist/index');
	}

	public function platform(){
		return view('apilist/platform');
	}

	public function love(){
		return view('apilist/love');
	}
}
