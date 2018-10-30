<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use think\Cache;

class Ad extends Base {

	public function info()
	{
		$data = $this->data;
		$res = service("ad")->info($data);
		return $this->apkReturn($res);
	}

	public function listBy()
	{
		$data = $this->data;
		$res = service("ad")->listBy($data);
		return $this->apkReturn($res);
	}
}
