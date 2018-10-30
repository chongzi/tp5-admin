<?php

namespace app\api\controller\v1;

use app\api\controller\Base;

class Common extends Base
{
    public function upload()
    {
        $res = model('news')->upload('image');
        if($res){
            return $this->apkReturn($res[0]);
        }
        return $this->apkReturn('上传失败', 0);
    }
}
