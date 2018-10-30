<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use think\Cache;

class Money extends Base {
	//提现
	public function changeMoney(){
		$data = $this->data;
		$model = service("money");
		$res = $model->changeMoney($data);
		if($res){
			return $this->apkReturn($res);
		}else{
			return $this->apkReturn($model->getError(),0);
		}
	}
}
