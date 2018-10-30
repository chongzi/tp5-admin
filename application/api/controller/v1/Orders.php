<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use base\payment\Pay;

class Orders extends Base
{

    public function payWay()
    {
        $payWay = [
            ['pay_way_name' => 'wxpay', 'pay_way_title' => '微信支付'],
            ['pay_way_name' => 'alipay', 'pay_way_title' => '支付宝'],
        ];

        return $this->apkReturn($payWay);
    }

    /**
     * 订单列表
     * @input   user_id
     *          app_id
     *          type
     *          page
     *          limit
     */
    public function lists()
    {

        $map = [];
        if (!isset($this->data['user_id'])) {
            return $this->apkReturn('参数错误', 0);
        }
        if (!isset($this->data['app_id'])) {
            return $this->apkReturn('参数错误', 0);
        }

        $map['user_id'] = $this->data['user_id'];
        $map['app_id'] = $this->data['app_id'];

        if (isset($this->data['type'])) {
            $map['type'] = $this->data['type'];
        }

        $page = isset($this->data['page']) ? $this->data['page'] : 1;
        $limit = isset($this->data['limit']) ? $this->data['limit'] : 0;

        $lists = logic('orders')->search($map, 'id desc', $page, $limit);
        $lists = $this->orderListFormat($lists);
        return $this->apkReturn($lists);
    }

    /**
     * 创建订单
     * @input   user_id
     *          app_id
     *          title
     *          money
     *          imeil
     *          pay_way_name
     *          agent_id
     *          goods_list
     */
    public function init()
    {

        if (!isset($this->data['user_id'])) {
            return $this->apkReturn('参数错误', 0);
        }
        if (!isset($this->data['app_id'])) {
            return $this->apkReturn('参数错误', 0);
        }
        $app_id = $this->data['app_id'];
        $user_id = $this->data['user_id'];

        $title = isset($this->data['title']) ? $this->data['title'] : '';
        $money = isset($this->data['money']) ? $this->data['money'] : '';
        $imeil = isset($this->data['imeil']) ? $this->data['imeil'] : '';
        $pay_way_name = isset($this->data['pay_way_name']) ? $this->data['pay_way_name'] : '';
        $agent_id = isset($this->data['agent_id']) ? $this->data['agent_id'] : '';
        $goods_list = isset($this->data['goods_list']) ? $this->data['goods_list'] : '';
        $goods_list = json_decode($goods_list, true);

        // 创建消费订单
        $pay_order_info = logic('orders')->order($app_id, $user_id, $title, $money, $imeil, $goods_list, $agent_id);
        if (!$pay_order_info) {
            return $this->apkReturn(logic('orders')->getError(), 0);
        }

        $user_info = logic('user')->info($user_id);
        if ($user_info['money'] > $pay_order_info['pt_money']) {
            // 余额足够，发起支付
            if (logic('orders')->pay($pay_order_info['order_sn'])) {
                return $this->apkReturn(new \sthClass, 2, '购买成功');
            } else {
                // 购买失败
                $this->apkReturn(logic('orders')->getError(), 0);
            }
        } else {
            // 计算充值金额
            $pay_money = $pay_order_info['pt_money'] - $user_info['money'];

            // 创建充值订单
            $charge_order_info = logic('orders')->charge($app_id, $user_id, $pay_money, $pay_way_name, $imeil, $pay_order_info['order_sn'], $agent_id);
            if (!$charge_order_info) {
                $this->apkReturn('订单创建失败！', 0);
            }

            // 余额不足，发起支付
            $agent_id = $user_info['agent_id'];
            $option = [
                'account' => logic('pay_account')->getAccount($app_id, $agent_id)
            ];
            $pay = new Pay($pay_way_name, $option);

            $payment['out_trade_no'] = $charge_order_info['order_sn'];
            $payment['title'] = $charge_order_info['title'];
            $payment['money'] = $charge_order_info['money'];
            $pay->init($payment);

            $params = $pay->app();

            $data = [
                'charge_order_sn' => $charge_order_info['order_sn'],
                'params' => $params,
            ];
            return $this->apkReturn($data);
        }
    }

    /**
     * 取消订单
     * @input   user_id
     *          order_sn
     */
    public function cancel()
    {

        if (!isset($this->data['user_id'])) {
            return $this->apkReturn('参数错误', 0);
        }
        if (!isset($this->data['order_sn'])) {
            return $this->apkReturn('参数错误', 0);
        }
        $order_sn = $this->data['order_sn'];
        $user_id = $this->data['user_id'];

        $map = [
            'user_id' => $this->data['user_id'],
            'order_sn' => $this->data['order_sn'],
            'type' => 1,
        ];

        $order = logic('orders')->infoBy($map);
        if (!$order) {
            return $this->apkReturn('订单不存在', 0);
        }

        if (logic('orders')->cancel($order_sn)) {
            return $this->apkReturn('取消成功');
        }

        return $this->apkReturn(logic('orders')->getError());
    }

    protected function orderListFormat($list)
    {
        $_list = [];
        foreach ($list as $item) {
            $_list[] = $this->orderFormat($item);
        }
        return $_list;
    }

    protected function orderFormat($order)
    {
        return [
            'order_sn' => $order['order_sn'],
            'title' => $order['title'],
            'type' => $order['type'],
            'money' => $order['money'],
            'payway_name' => $order['payway_name'],
            'add_time' => $order['add_time'],
            'status' => $order['status'],
            'msg' => $order['msg'],
            'ip' => $order['ip'],
        ];
    }
}
