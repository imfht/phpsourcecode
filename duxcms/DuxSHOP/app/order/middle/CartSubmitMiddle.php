<?php

/**
 * 购物车提交
 */

namespace app\order\middle;


class CartSubmitMiddle extends \app\base\middle\BaseMiddle {


    /**
     * 媒体信息
     */
    protected function meta() {
        $this->setMeta('结算购物车');
        $this->setName('结算购物车');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '结算购物车',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $this->params['user_id'] = intval($this->params['user_id']);
        $addId = intval($this->params['add_id']);
        $addInfo = target('order/OrderAddress')->getAddress($this->params['user_id'], $addId);
        $addList = target('order/OrderAddress')->loadList(['user_id' => $this->params['user_id']]);
        $_list = target('order/Cart', 'service')->getList($this->params['user_id']);
		$list = [];

		$quick = $this->params['quick'];

		if($quick) {
            $quickArray = explode('-', $quick);
            $app = $quickArray[0];
            $hasId = $quickArray[1];
            $subId = $quickArray[2];
            $qty = $quickArray[3];
		    $info = target($app . '/' . $app, 'service')->getCart($this->params['user_id'], $hasId, $subId, $qty);
		    if(!$info) {
		        $this->stop(target($app . '/' . $app, 'service')->getError());
            }
            $info['total'] = $info['price'] * $info['qty'];
            $info['items'] = $info['qty'];
            $list[] = $info;
        }else {
            if($_list) {
                foreach ($_list as $k => $v) {
                    if ($v['checked']) {
                        $list[$k] = $v;
                    }
                }
            }
            $info = target('order/Cart', 'service')->getCart($this->params['user_id']);
        }

        $payAccountInfo = target('member/PayAccount')->getWhereInfo([
            'A.user_id' => $this->params['user_id']
        ]);
		if(empty($payAccountInfo['password'])) {
		    return $this->stop('请先设置支付密码！', 500, url('member/Setting/payPassword'));
        }

        if (empty($list)) {
            return $this->stop('请选择您要结算的商品！');
        }

        $orderPrice = 0;
        $deliveryPrice = 0;
        $discountsPrice = 0;
        $orderData = target('order/Order', 'service')->splitOrder($addInfo['province'], $list);
        foreach ($orderData as $vo) {
            $deliveryPrice += $vo['delivery_price'];
            $orderPrice += $vo['order_price'];
        }

        $urlParams = [
            'add_id' => $addId,
            'quick' => $quick
        ];

        $totalPrice = price_format($deliveryPrice + $orderPrice - $discountsPrice);
        $currency = target('order/Order', 'service')->getCurrency($list);

        $couponList = target('order/OrderCouponLog')->loadList([
            'A.user_id' => $this->params['user_id'],
            'A.status' => 0,
            'A.del' => 0,
            '_sql' => 'A.end_time >= ' . time(),
        ]);

        foreach ($orderData as $key => $data) {
            $couponData = [];
            foreach ($couponList as $k => $v) {
                if (target($v['typeInfo']['target'])->hasCoupon($v, $data)) {
                    $couponData[] = $v;
                }
            }
            $orderData[$key]['coupon'] = $couponData;
        }

        return $this->run([
            'cartData' => $orderData,
            'list' => $list,
            'info' => $info,
            'couponList' => $couponList,
            'deliveryPrice' => price_format($deliveryPrice),
            'orderPrice' =>  price_format($orderPrice),
            'discountsPrice' =>  price_format($discountsPrice),
            'totalPrice' =>  price_format($totalPrice),
            'addList' => $addList,
            'addInfo' => $addInfo,
            'urlParams' => $urlParams,
            'currencyAppend' => $currency['append'],
            'currencyExchange' => $currency['exchange'],
            'invoiceClass' => target('order/OrderInvoiceClass')->loadList()
        ]);
    }

    protected function Take() {
        $addId = intval($this->params['add_id']);
        $addInfo = target('order/OrderAddress')->getAddress($this->params['user_id'], $addId);
        $takeList = target('order/OrderTake')->loadList([
            'province' => $addInfo['province'],
            'status' => 1
        ]);
        return $this->run([
            'takeList' => $takeList
        ]);
    }

    protected function post() {

        $this->params['user_id'] = intval($this->params['user_id']);
        $codStatus = $this->params['cod_status'];
        $couponId = $this->params['coupon_id'];
        $takeId = $this->params['take_id'];
        $addId = $this->params['add_id'];
        $remark = $this->params['remark'];

        $quick = $this->params['quick'];
        $data = [];
        if($quick) {
            $quickArray = explode('-', $quick);
            $app = $quickArray[0];
            $hasId = $quickArray[1];
            $subId = $quickArray[2];
            $qty = $quickArray[3];
            $info = target($app . '/' . $app, 'service')->getCart($this->params['user_id'], $hasId, $subId, $qty);
            if(!$info) {
                $this->stop(target($app . '/' . $app, 'service')->getError());
            }
            $info['total'] = $info['price'] * $info['qty'];
            $info['items'] = $info['qty'];
            $data[] = $info;
        }else {
            $list = target('order/Cart', 'service')->getList($this->params['user_id']);
            if($list) {
                foreach ($list as $k => $v) {
                    if ($v['checked']) {
                        $data[$k] = $v;
                    }
                }
            }
        }
        if (empty($data)) {
            return $this->stop('结算商品不存在!');
        }

        $target = target('order/Order', 'service');
        $addInfo = target('order/OrderAddress')->getAddress($this->params['user_id'], $addId);

        if (empty($addInfo)) {
            return $this->stop('收货地址不存在,请重新选择!');
        }
        $data = $target->splitOrder($addInfo['province'], $data);

        //重组数据
        foreach ($data as $key => $vo) {
            $attr = [
                'coupon_id' => intval($couponId[$key]),
                'take_id' => intval($takeId[$key]),
                'cod_status' => intval($codStatus[$key]),
                'remark' => html_clear($remark[$key]),
            ];

            if($this->params['invoice'][$key]) {
                if(empty($this->params['invoice_class'][$key]) || empty($this->params['invoice_name'][$key])) {
                    return $this->stop('请输入发票抬头并选择发票内容！');
                }
                if($this->params['invoice_type'][$key] && empty($this->params['invoice_label'][$key])) {
                    return $this->stop('请输入纳税人识别号！');
                }
                $attr['invoice'] = [
                    'type' => intval($this->params['invoice_type'][$key]),
                    'class_id' => intval($this->params['invoice_class'][$key]),
                    'name' => html_clear($this->params['invoice_name'][$key]),
                    'number' => html_clear($this->params['invoice_label'][$key]),
                ];
            }
            $data[$key]['attr'] = $attr;
        }

        $orderNos = $target->addOrder($this->params['user_id'], $data, $addId);
        if (!$orderNos) {
            return $this->stop($target->getError());
        }
        if(empty($quick)) {
            target('order/Cart', 'service')->clear($this->params['user_id']);
        }

        $orderList = target('order/Order')->loadList([
            '_sql' => 'order_no in(' . implode(',', $orderNos) . ')'
        ]);

        $deliveryPrice = 0;
        $payPrice = 0;
        $app = [];

        foreach ($orderList as $vo) {
            if (!$takeId) {
                $deliveryPrice += $vo['delivery_price'];
            }
            $payPrice += $vo['pay_price'];
            $app[] = $vo['order_app'];
        }
        $app = array_unique($app);
        $app = implode('|', $app);

        $accountInfo = target('member/PayAccount')->getWhereInfo([
            'A.user_id' => $this->params['user_id']
        ]);

        return $this->run(['cod_status' => $codStatus, 'order_no' => implode('|', $orderNos), 'pay_price' => $payPrice, 'delivery_price' => $deliveryPrice, 'user_money' => $accountInfo['money'], 'app' => $app]);
    }

}