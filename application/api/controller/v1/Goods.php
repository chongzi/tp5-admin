<?php

namespace app\api\controller\v1;

use app\api\controller\Base;

class Goods extends Base
{

    protected $user_id = 0;

    /**
     * 商品列表
     * @input    type_id
     *           app_id
     *           page
     *           limit
     */
    public function index()
    {
        $map = [
            'status' => 1,
        ];
        if (isset($this->data['type_id'])) {
            $map['type_id'] = $this->data['type_id'];
        }
        if (isset($this->data['app_id'])) {
            $map['app_id'] = $this->data['app_id'];
        }
        if (isset($this->data['user_id'])) {
            $this->user_id = $this->data['user_id'];
        }

        $page = isset($this->data['page']) ? $this->data['page'] : 1;
        $limit = isset($this->data['limit']) ? $this->data['limit'] : 0;

        $goods = logic('goods')->search($map, 'sort desc, id desc', $page, $limit);
        if ($goods) {
            $goods = $this->goodListFormat($goods);
            return $this->apkReturn($goods);
        }
        return $this->apkReturn(logic('goods')->getError(), 0);
    }

    /**
     * 商品详情
     * @input       goods_id
     */
    public function info()
    {
        if (!isset($this->data['goods_id'])) {
            return $this->apkReturn('参数错误', 0);
        }

        $goods = logic('goods')->info($this->data['goods_id']);

        if ($goods) {
            return $this->apkReturn($goods);
        }
        return $this->apkReturn('商品不存在', 0);
    }

    protected function goodListFormat($list)
    {
        $_list = [];
        foreach ($list as $item) {
            $_list[] = $this->goodFormat($item);
        }
        return $_list;
    }

    protected function goodFormat($good)
    {
        $pay_price = number_format($good['m_price'], 0, '.', '');

        //debug
        $debug_imeil = config('debug_imeil');
        if (isset($debug_imeil) && isset($this->data['imeil']) && $this->data['imeil'] == $debug_imeil) {
            $pay_price = 0.01;
        }

        return [

            'id' => $good['id'],
            'name' => $good['name'],
            'type_id' => $good['type_id'],
            'type_relate_val' => $good['type_relate_val'],
            'app_id' => $good['app_id'],
            'img' => $this->realPath($good['img']),
            'desp' => $good['desp'],
            'price' => number_format($good['price'], 0, '.', ''),
            'm_price' => number_format($good['m_price'], 0, '.', ''),
            'vip_price' => number_format($good['vip_price'], 0, '.', ''),
            'unit' => $good['unit'],
            'use_time_limit' => $good['use_time_limit'],
            'sort' => $good['sort'],
            'status' => $good['status'],

            // 实际支付价格
            'pay_price' => $pay_price,
        ];
    }
}
