<?php

namespace app\admin\model;

use think\Model;

class AuthRule extends Model
{
    use TextParserTrait;

    protected $insert = ['status' => 1];
}
