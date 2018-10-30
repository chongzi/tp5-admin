<?php

namespace app\api\controller;

use base\payment\Pay;

class Notify
{
    public function index($pay_way_name, $account = 'default')
    {
        $option = [
            'account' => $account,
        ];
        $pay = new Pay($pay_way_name, $option);
        return $pay->notifyUrl();
    }
}
