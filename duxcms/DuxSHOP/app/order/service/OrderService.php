<?php

namespace app\order\service;
/**
 * 订单处理
 */
class OrderService extends \app\base\service\BaseService {
    /**
     * @var string
     */
    private $model = 'order/Order';

    /**
     * 添加订单
     * @param $userId
     * @param array $proData
     * @param int $addId
     * @return bool
     */
    public function addOrder($userId, $proData = [], $addId = 0) {

        if (empty($proData)) {
            return $this->error('提交的订单暂无商品!');
        }

        if (empty($proData)) {
            return $this->error('购物车内暂无商品！');
        }
        foreach ($proData as $key => $vo) {
            $target = target($vo['app'] . '/' . 'Order', 'service');
            if (!$target->refreshCart($userId, $vo)) {
                return $this->error($target->getError());
            }
        }

        $addInfo = target('order/OrderAddress')->getAddress($userId, $addId);

        target($this->model)->beginTransaction();
        $orderIds = [];
        $orderNos = [];
        $couponUse = [];

        foreach ($proData as $key => $vo) {
            $orderData = [];
            $payPrice = $vo['order_price'];
            $orderPrice = $vo['order_price'];
            $discountPrice = 0;
            $deliveryPrice = 0;

            //商品数量
            $num = 0;
            foreach ($vo['items'] as $data) {
                $num += $data['qty'];
            }
            //货币信息
            $currency = $this->getCurrency($vo['items']);
            if (bccomp(0, $payPrice, 2) !== -1) {
                $payPrice = 0;
            }

            //自提点
            if($vo['attr']['take_id']) {
                $takeInfo = target('order/OrderTake')->getWhereInfo([
                    'province' => $addInfo['province'],
                    'city' => $addInfo['city'],
                    'take_id' => $vo['attr']['take_id']
                ]);
                if (empty($takeInfo)) {
                    return $this->error('所选自提点不可用!');
                }
                $orderData['take_id'] = $vo['attr']['take_id'];
            }else {
                $deliveryPrice = $vo['delivery_price'];
            }

            //优惠券使用
            $couponInfo = [];
            if($vo['attr']['coupon_id']) {
                $couponInfo = target('order/OrderCouponLog')->getWhereInfo([
                    'A.log_id' => $vo['attr']['coupon_id'],
                    'A.user_id' => $userId,
                    'A.status' => 0,
                    'A.del' => 0,
                    '_sql' => 'A.end_time >= ' . time(),
                ]);
                if (empty($couponInfo)) {
                    return $this->error('所选优惠券不可用!');
                }
                if(in_array($vo['attr']['coupon_id'], $couponUse)) {
                    return $this->error('多个订单不能同时使用优惠券!');
                }
                $couponUse[] = $vo['attr']['coupon_id'];
                $couponIds = target($couponInfo['typeInfo']['target'])->hasCoupon($couponInfo, $vo);
                if ($couponIds) {
                    $payPrice = price_calculate($payPrice, '-', $couponInfo['money']);
                    $discountPrice = price_calculate($discountPrice, '+', $couponInfo['money']);
                    target('order/OrderCouponLog')->edit([
                        'log_id' => $couponInfo['log_id'],
                        'status' => 1
                    ]);
                    $orderData['order_coupon'] = $couponInfo['log_id'];
                    $couponInfo['has_id'] = $couponIds;
                }else {
                    $couponInfo = [];
                }
            }

            $curPro = reset($vo['items']);
            $orderNo = log_no($userId);
            $orderNos[] = $orderNo;

            $orderData['order_user_id'] = $userId;
            $orderData['order_app'] = $vo['app'];


            $curPro['name'] = str_len(\dux\lib\Str::symbolClear($curPro['name']), 20);

            if ($num > 1) {
                $orderData['order_title'] = $curPro['name'] . '等' . $num . '件商品';
            } else {
                $orderData['order_title'] = $curPro['name'];
            }

            $orderData['order_image'] = $curPro['image'];
            $orderData['order_price'] = $orderPrice;
            $orderData['order_sum'] = $vo['order_sum'];
            //$orderData['order_currency'] = serialize($currency);
            $orderData['order_remark'] = str_len(html_clear($vo['attr']['remark']), 250);

            $orderData['receive_name'] = $addInfo['name'];
            $orderData['receive_tel'] = $addInfo['tel'];
            $orderData['receive_province'] = $addInfo['province'];
            $orderData['receive_city'] = $addInfo['city'];
            $orderData['receive_region'] = $addInfo['region'];
            $orderData['receive_address'] = $addInfo['address'];
            $orderData['receive_zip'] = $addInfo['zip'];

            $orderData['order_no'] = $orderNo;
            $orderData['order_status'] = 1;
            $orderData['order_create_time'] = time();
            $orderData['order_complete_status'] = 0;
            $orderData['order_ip'] = \dux\lib\Client::getUserIp();

            $orderData['pay_type'] = $vo['attr']['cod_status'] ? 0: 1;
            $orderData['pay_status'] = 0;
            $orderData['pay_id'] = 0;
            $orderData['pay_price'] = $payPrice;
            $orderData['pay_discount'] = $discountPrice;
            $orderData['pay_currency'] = serialize($currency);
            $orderData['delivery_status'] = 0;
            $orderData['delivery_price'] = $deliveryPrice;


            $orderId = target($this->model)->add($orderData);
            if (!$orderId) {
                return $this->error(target($this->model)->getError());
            }

            $orderData['order_id'] = $orderId;

            $goodsList = [];

            foreach ($vo['items'] as $data) {
                $goodsData = [
                    'order_id' => $orderId,
                    'has_id' => $data['app_id'],
                    'sub_id' => $data['id'],
                    'user_id' => $userId,
                    'goods_no' => $data['item_no'],
                    'goods_qty' => $data['qty'],
                    'goods_price' => $data['price'],
                    'goods_cost_price' => $data['cost_price'],
                    'goods_market_price' => $data['market_price'],
                    'goods_weight' => $data['weight'],
                    'goods_options' => serialize($data['options']),
                    'goods_name' => $data['name'],
                    'goods_image' => $data['image'],
                    'goods_url' => $data['url'],
                    'price_total' => price_calculate($data['price'], '*', $data['qty']),
                    'extend' => serialize($data['extend']),
                    'comment_status' => $data['comment_status'] ? 1 : 0,
                    'refund_status' => 0,
                    'goods_point' => $data['point'],
                    'goods_currency' => serialize($data['currency']),
                ];
                $goodsList[] = $goodsData;

            }

            if($couponInfo) {
                $hasGoodsData = [];
                foreach ($goodsList as $k => $data) {
                    if(in_array($data['sub_id'], $couponInfo['has_id']) || empty($couponInfo['has_id'])) {
                        $hasGoodsData[$k] = $data;
                    }
                }
                if($hasGoodsData) {
                    $sumPrice = 0;
                    foreach ($hasGoodsData as $goods) {
                        $sumPrice = price_calculate($sumPrice, '+', $goods['price_total'], 2);
                    }
                    foreach ($hasGoodsData as $k => $goods) {
                        $goodsDiscount = round($goods['price_total']/$sumPrice*$couponInfo['money'], 2);
                        $goodsList[$k]['price_total'] = price_calculate($goods['price_total'], '-', $goodsDiscount, 2);
                        $goodsList[$k]['price_discount'] = $goodsDiscount;
                    }
                }

            }

            foreach ($goodsList as $goods) {
                if (!target('order/OrderGoods')->add($goods)) {
                    target('order/Order')->rollBack();
                    return $this->error('订单提交失败!');
                }
            }

            if($vo['attr']['invoice']) {
                $status = target('order/OrderInvoice')->add(array_merge($vo['attr']['invoice'], [
                    'create_time' => time(),
                    'user_id' => $userId,
                    'order_id' => $orderId,
                ]));
                if (!$status) {
                    target('order/Order')->rollBack();
                    return $this->error('发票提交失败!');
                }
            }

            $target = target($vo['app'] . '/Order', 'service');
            if (!$target->addOrder($orderData, $goodsList, $addId)) {
                target('order/Order')->rollBack();
                return $this->error($target->getError());
            }

            $hookList = run('service', 'Order', 'hookAddOrder', [$orderData, $goodsList]);
            if (!empty($hookList)) {
                foreach ($hookList as $a => $v) {
                    if (!$v) {
                        return $this->error(target($a . '/Order', 'service')->getError());
                    }
                }
            }

            if (!$this->addLog($orderId, 1, '您提交了订单，请等待卖家确认')) {
                target('order/Order')->rollBack();
                return $this->error('订单日志记录失败!');
            }
            $orderIds[] = $orderId;
        }
        target('order/Order')->commit();

        return $this->success($orderNos);
    }

    /**
     * 支付订单
     * @param $orderPayNo
     * @param $money
     * @param $payName
     * @param $payNo
     * @param $payId
     * @param $payWay
     * @return bool
     */
    public function payOrder($orderPayNo, $money, $payName, $payNo, $payId = 0, $payWay = 'system') {
        if (empty($orderPayNo) || empty($payName) || empty($payNo)) {
            return $this->error('订单信息错误!');
        }

        $orderPay = target('order/OrderPay')->getWhereInfo([
            'pay_no' => $orderPayNo,
        ]);

        if (empty($orderPay)) {
            return $this->error('支付订单不存在!');
        }

        $where = [];
        $where['_sql'] = 'order_id in(' . $orderPay['order_ids'] . ')';
        $orderList = target('order/Order')->LoadList($where, 0, '', true);

        if (empty($orderList)) {
            return $this->error('订单不存在!');
        }


        $orderData = [];

        foreach ($orderList as $key => $vo) {
            $orderData[$vo['order_app']][$key] = $vo;
        }


        //过滤被取消订单
        $orderIds = [];
        $orderNos = [];

        foreach ($orderList as $key => $vo) {
            $orderIds[] = $vo['order_id'];
            $orderNos[] = $vo['order_no'];
        }

        $sumPrice = 0;
        foreach ($orderList as $key => $vo) {
            $sumPrice = price_calculate($vo['total_price'], '+', $sumPrice);
        }

        if (bccomp($money, $sumPrice, 2) === -1) {
            return $this->error('支付金额不正确');
        }

        $orderNos = [];
        foreach ($orderList as $vo) {
            $orderNos[] = $vo['order_no'];
        }

        $payList = target('member/PayConfig')->typeList();
        $payTypeInfo = $payList[$payWay];

        $orderInfo = current($orderList);

        if (!$payId) {
            $payId = target($payTypeInfo['target'], 'pay')->addLog([
                'pay_no' => $payNo,
                'pay_name' => $payName,
                'user_id' => $orderList[0]['order_user_id'],
                'title' => '订单支付',
                'remark' => $orderInfo['order_title'],
                'money' => $sumPrice,
            ]);
            if (empty($payId)) {
                return $this->error(target($payTypeInfo['target'], 'pay')->getError());
            }
        }

        if (!empty($orderIds)) {
            //更改订单支付状态
            foreach ($orderList as $vo) {
                $vo['pay_data'][] = [
                    'way' => $payWay,
                    'id' => $payId,
                    'money' => $vo['total_price']
                ];
                $status = target('order/Order')->where([
                    'order_id' => $vo['order_id'],
                ])->data([
                    'pay_status' => 1,
                    'pay_time' => time(),
                    'pay_data' => serialize($vo['pay_data']),
                ])->update();
                if (!$status) {
                    return $this->error('订单支付失败!');
                }
            }
        }
        foreach ($orderList as $order) {
            if (!$this->addLog($order['order_id'], 1, '订单支付成功,支付方式【' . $payName . '】')) {
                return $this->error('订单日志记录失败!');
            }
        }

        //付款成功回调
        foreach ($orderData as $app => $rows) {
            if (!target($app . '/Order', 'service')->payOrder($rows)) {
                return $this->error(target($app . '/Order', 'service')->getError());
            }
        }

        //被动接口
        $hookList = run('service', 'Order', 'hookPayOrder', [$orderList]);

        if (!empty($hookList)) {
            foreach ($hookList as $a => $vo) {
                if (!$vo) {
                    return $this->error(target($a . '/Order', 'service')->getError());
                }
            }
        }
        //通知接口
        foreach ($orderIds as $id) {
            $this->noticeOrder('pay', $id, [
                'pay_no' => $payNo,
                'pay_name' => $payName,
                'pay_money' => $sumPrice,
                'pay_time' => time(),
            ]);
        }

        return $this->success();
    }

    /**
     * 创建合并支付
     * @param $userId
     * @param $orderIds
     * @return bool
     */
    public function addPay($userId, $orderIds) {
        $payNo = log_no($userId);
        $data = [
            'user_id' => $userId,
            'pay_no' => $payNo,
            'order_ids' => $orderIds,
            'time' => time(),
        ];
        if (!target('order/orderPay')->add($data)) {
            return $this->error('支付信息创建失败!');
        }

        return $this->success($payNo);
    }

    /**
     * 订单/商品退款
     * @param $hasId
     * @param int $type
     * @param $price
     * @return bool
     */
    public function refundOrder($hasId, $type = 0, $price = 0) {


        $goodsIds = [];
        if ($type) {
            $goodsList = target('order/OrderGoods')->loadList([
                'order_id' => $hasId,
                '_sql' => 'service_status <> 2'
            ]);
            foreach ($goodsList as $vo) {
                $goodsIds[] = $vo['id'];
            }
        } else {
            $goodsIds[] = $hasId;
        }

        if (empty($goodsIds)) {
            return $this->error('商品数据不存在!');
        }

        $goodsList = target('order/OrderGoods')->loadList([
            '_sql' => 'id in (' . implode(',', $goodsIds) . ')'
        ]);

        $orderInfo = target('order/Order')->getWhereInfo([
            'order_id' => $goodsList[0]['order_id']
        ]);

        $totalPrice = 0;
        $currencyList = target('member/MemberCurrency')->typeList();
        $currency = [];
        foreach ($goodsList as $info) {
            $id = $info['id'];
            $save = target('order/OrderGoods')->edit([
                'id' => $id,
                'service_status' => 2
            ]);
            if (!$save) {
                return $this->error('退款处理失败!');
            }
            if ($type) {
                $price = $info['price_total'];
            }
            $totalPrice = price_calculate($totalPrice, '+', $price);
            if (!$info['goods_currency'] || bccomp(0, $info['goods_currency']['money'], 2) !== -1) {
                continue;
            }
            $currency[$info['goods_currency']['type']] = price_calculate($currency[$info['goods_currency']['type']], '+', $info['goods_currency']['money']);
        }

        $goodsCount = target('order/OrderGoods')->countList([
            'order_id' => $orderInfo['order_id'],
            'service_status' => 0
        ]);

        $orderStatus = 1;
        if (!$goodsCount) {
            $orderStatus = 0;
        }

        //退货币
        foreach ($currency as $key => $vo) {
            $status = target($currencyList[$key]['target'], 'service')->account([
                'user_id' => $orderInfo['order_user_id'],
                'money' => $vo,
                'type' => 1,
                'deduct' => 1,
                'title' => '订单退款',
                'pay_name' => '账号支付',
                'remark' => '订单【' . $orderInfo['order_no'] . '】取消退款'
            ]);
            if (!$status) {
                return $this->error(target($currencyList[$key]['target'], 'service')->getError());
            }
        }
        //退款
        if (bccomp($totalPrice, 0, 2) === 1) {
            $status = target('member/Finance', 'service')->account([
                'user_id' => $orderInfo['order_user_id'],
                'money' => $totalPrice,
                'pay_name' => '账号支付',
                'type' => 1,
                'title' => '订单退款',
                'remark' => '订单【' . $orderInfo['order_no'] . '】商品退款'
            ]);
            if (!$status) {
                return $this->error(target('member/Finance', 'service')->getError());
            }
        }
        //更新订单信息
        $status = target('order/Order')->edit([
            'order_id' => $orderInfo['order_id'],
            'order_status' => $orderStatus,
            'refund_price' => price_calculate($orderInfo['refund_price'], '+', $totalPrice)
        ]);
        if (!$status) {
            return $this->error(target('order/Order', 'service')->getError());
        }

        $hookList = run('service', 'Order', 'hookRefundOrder', [$hasId, $type, $totalPrice]);
        if (!empty($hookList)) {
            foreach ($hookList as $a => $vo) {
                if (!$vo) {
                    $this->error(target($a . '/Order', 'service')->getError());
                }
            }
        }

        return $this->success();
    }


    /**
     * 取消订单
     * @param string $ids
     * @param bool $refund
     * @param array $goodsIds
     * @return bool
     */
    public function cancelOrder($ids = '', $refund = true) {
        if (empty($ids)) {
            return $this->error('ID参数未知');
        }

        $status = target('order/Order')->where([
            '_sql' => 'order_id in (' . $ids . ')',
        ])->data([
            'order_status' => 0,
            'order_close_time' => time(),
        ])->update();
        if (!$status) {
            return $this->error('取消订单失败!');
        }


        $orderList = target('order/Order')->loadList([
            '_sql' => 'order_id in (' . $ids . ')',
        ]);

        if (empty($orderList)) {
            return $this->success();
        }

        $orderData = [];
        $appData = [];
        $refundStatus = false;
        foreach ($orderList as $key => $vo) {
            $orderData[$vo['order_user_id']][$key] = $vo;
            $appData[$vo['order_app']][$key] = $vo;
            if (!$this->addLog($vo['order_id'], 1, '订单取消成功,如已付款款项将退回到您的余额中！')) {
                return $this->error('订单日志记录失败!');
            }
            $goodsCount = target('order/OrderGoods')->countList([
                'order_id' => $vo['order_id'],
                'service_status' => 1
            ]);
            if ($goodsCount) {
                return $this->error('订单中有售后中商品，暂时无法取消!');
            }
            if($vo['pay_status']) {
                $refundStatus = true;
            }
        }
        //取消方法
        foreach ($appData as $app => $vo) {
            if (!target($app . '/Order', 'service')->cancelOrder($vo)) {
                return $this->error(target($app . '/Order', 'service')->getError());
            }
        }
        if($refund) {
            $refund = $refundStatus ? true : false;
        }

        //被动接口
        $hookList = run('service', 'Order', 'hookCancelOrder', [$orderList, $appData]);

        if (!empty($hookList)) {
            foreach ($hookList as $a => $vo) {
                if (!$vo) {
                    return $this->error(target($a . '/Order', 'service')->getError());
                }
            }
        }

        if (!$refund) {
            return $this->success();
        }

        //退货币
        $currencyList = target('member/MemberCurrency')->typeList();
        foreach ($orderList as $order) {
            $currency = [];
            $goodsList = target('order/OrderGoods')->loadList([
                'order_id' => $order['order_id'],
                'service_status' => 0
            ]);
            foreach ($goodsList as $vo) {
                $currency[] = $vo['goods_currency'];
            }
            foreach ($currency as $key => $vo) {
                if ($vo['status']) {
                    if (!target($currencyList[$vo['type']]['target'], 'service')->incAccount($order['order_user_id'], $vo['money'], '账户支付', '订单取消', $order['order_no'], '订单取消退款')) {
                        return $this->error(target($currencyList[$vo['type']]['target'], 'service')->getError());
                    }
                }
            }
        }

        //退款操作
        $payList = target('member/PayConfig')->typeList();
        foreach ($orderData as $userId => $list) {
            foreach ($orderList as $order) {
                $payData = $order['pay_data'];
                $payData = $payData[0];
                $payWay = $payData['way'];
                $payTypeInfo = $payList[$payWay];
                $money = price_calculate($order['total_price'], '-', $order['refund_price']);
                if (bccomp(0, $money, 2) !== -1) {
                    continue;
                }
                $payId = target($payTypeInfo['target'], 'pay')->refund([
                    'user_id' => $userId,
                    'id' => $payData['id'],
                    'title' => '订单退款',
                    'remark' => '订单【' . $order['order_no'] . '】退款',
                    'money' => $money,
                ]);
                if (empty($payId)) {
                    return $this->error(target($payTypeInfo['target'], 'pay')->getError());
                }
                $status = target('order/Order')->where([
                    'order_id' => $order['order_id'],
                ])->data([
                    'refund_price' => $order['total_price'],
                ])->update();
                if (!$status) {
                    return $this->error('取消订单失败!');
                }

            }
        }
        return $this->success();
    }


    /**
     * 订单配货
     * @param $orderId
     * @param string $remark
     * @param bool $log
     * @return bool
     */
    public function parcelOrder($orderId, $remark = '', $log = true) {
        $status = target('order/Order')->where([
            'order_id' => $orderId,
        ])->data([
            'parcel_status' => 1,
        ])->update();
        if (!$status) {
            return $this->error('配货状态更改失败!');
        }
        if (!$log) {
            return $this->success();
        }
        $time = time();
        $data = [
            'order_id' => $orderId,
            'create_time' => $time,
            'status' => 1,
            'remark' => $remark,
        ];
        $data['log'] = target('order/OrderParcel')->addLog([], '生成发货单,待工作人员配货', '', $time);
        if (!target('order/OrderParcel')->add($data)) {
            return $this->error('配货单生成失败!');
        }
        if (!$this->addLog($orderId, 1, '订单确认成功,等待工作人员配货')) {
            return $this->error('订单日志记录失败!');
        }
        return $this->success();
    }

    /**
     * 订单发货
     * @param $orderId
     * @param $ids
     * @param $deliveryType
     * @param string $name
     * @param string $no
     * @param string $remark
     * @param bool $log
     * @return bool
     */
    public function deliveryOrder($orderId, $ids, $deliveryType, $name = '', $no = '', $remark = '', $log = true) {
        if (empty($orderId)) {
            return $this->error('传递参数有误!');
        }

        if(empty($ids)) {
            return $this->error('请先选择发货商品!');
        }

        $deliveryType = $deliveryType ? 1 : 0;
        $model = target('order/Order');
        $orderInfo = $model->getInfo($orderId);

        if (empty($orderInfo)) {
            return $this->error('订单不存在!');
        }

        $goodsList = target('order/OrderGoods')->loadList([
            'order_id' => $orderId,
            '_sql' => 'id in (' . implode(',', $ids) . ')',
            'delivery_status' => 0,
        ]);

        if (empty($goodsList)) {
            return $this->error('订单暂无需发货商品！');
        }

        $deliveryId = 0;
        $time = time();
        if ($deliveryType) {
            if (empty($name) || empty($no)) {
                return $this->error('请先选择发货商品!');
            }
            $data = [
                'order_id' => $orderId,
                'delivery_name' => html_clear($name),
                'delivery_no' => html_clear($no),
                'create_time' => $time,
                'remark' => html_clear($remark),
            ];
            $deliveryId = target('order/OrderDelivery')->add($data);
            if (!$deliveryId) {
                return $this->error(target('order/OrderDelivery')->getError());
            }
        }

        //完成订单状态
        $countGoods = target('order/OrderGoods')->countList(['order_id' => $orderId, 'delivery_status' => 0]);
        if ($countGoods == count($goodsList)) {
            $status = $model->edit([
                'order_id' => $orderId,
                'delivery_status' => 1,
            ]);
            if (!$status) {
                return $this->error('订单发货失败!');
            }
        }

        //设置货品状态
        foreach ($goodsList as $vo) {
            target('order/OrderGoods')->edit([
                'id' => $vo['id'],
                'delivery_type' => $deliveryType,
                'delivery_id' => $deliveryId,
                'delivery_status' => 1,
            ]);
        }

        //设置配货状态
        $status = target('order/OrderParcel')->where([
            'order_id' => $orderId,
        ])->data([
            'status' => 2,
        ])->update();
        if (!$status) {
            return $this->error('订单配货失败!');
        }

        $app = $orderInfo['order_app'];
        if (!target($app . '/Order', 'service')->deliveryOrder($orderInfo, $ids, $deliveryType)) {
            return $this->error(target($app . '/Order', 'service')->getError());
        }

        //被动接口
        $hookList = run('service', 'Order', 'hookDeliveryOrder', [
            'data' => $orderInfo,
        ]);

        if (!empty($hookList)) {
            foreach ($hookList as $a => $vo) {
                if (!$vo) {
                    return $this->error(target($a . '/Order', 'service')->getError());
                }
            }
        }

        if ($log) {
            if ($deliveryType) {
                $this->addLog($orderId, 1, '您的订单已出库,发货物流【' . $name . '】,物流单号【' . $no . '】');
            } else {
                $this->addLog($orderId, 1, '您的订单已发货，请等待收货');
            }
            $this->noticeOrder('delivery', $orderId, [
                'delivery_no' => $no ? $no : '无',
                'delivery_name' => $name ? $name : '无需物流',
                'delivery_time' => $time,
            ]);
        }

        return $this->success();
    }

    /**
     * 确认收货订单
     * @param $orderId
     * @return bool
     */
    public function confirmOrder($orderId) {
        $model = target('order/Order');
        $orderInfo = $model->getInfo($orderId);
        $app = $orderInfo['order_app'];
        $status = $model->edit([
            'order_id' => $orderId,
            'order_complete_status' => 1,
            'order_complete_time' => time(),
        ]);

        if (!$status) {
            return $this->error('订单确认失败!');
        }

        $status = target('order/OrderDelivery')->where([
            'order_id' => $orderId,
        ])->data([
            'receive_status' => 1,
            'receive_time' => time(),
        ])->update();

        if (!$status) {
            return $this->error('订单确认失败!');
        }

        if (!$this->addLog($orderId, 1, '您的订单已确认收货,欢迎您再次光临！')) {
            return $this->error('订单日志记录失败!');
        }

        if (!target($app . '/Order', 'service')->confirmOrder($orderInfo)) {
            return $this->error(target($app . '/Order', 'service')->getError());
        }

        //处理积分
        $point = 0;
        $orderGoods = target('order/OrderGoods')->loadList([
            'order_id' => $orderId,
            'service_status' => 0
        ]);

        foreach ($orderGoods as $vo) {
            $point += $vo['goods_point'] * $vo['goods_qty'];
        }

        if ($point) {
            $status = target('member/Points', 'service')->account([
                'user_id' => $orderInfo['order_user_id'],
                'money' => $point,
                'type' => 1,
                'deduct' => 1,
                'title' => '订单奖励',
                'remark' => '订单完成增加积分',
            ]);

            if (!$status) {
                return $this->error(target('member/Points', 'service')->getError());
            }
        }

        //被动接口
        $hookList = run('service', 'Order', 'hookConfirmOrder', [
            'data' => $orderInfo,
        ]);

        if (!empty($hookList)) {
            foreach ($hookList as $a => $vo) {
                if (!$vo) {
                    return $this->error(target($a . '/Order', 'service')->getError());
                }
            }
        }

        $this->noticeOrder('complete', $orderId);

        return $this->success();
    }

    /**
     * 增加订单日志
     * @param $orderId
     * @param int $type
     * @param $msg
     * @param string $remark
     * @return mixed
     */
    public function addLog($orderId, $type = 1, $msg, $remark = '') {
        return target('order/OrderLog')->add([
            'order_id' => $orderId,
            'msg' => $msg,
            'remark' => $remark,
            'time' => time(),
            'type' => $type,
            'ip' => \dux\lib\Client::getUserIp(),
        ]);
    }

    /**
     * 获取订单跟踪记录
     * @param $deliveryId
     * @return bool
     */
    public function getWaybillLog($deliveryId) {
        $deliveryInfo = target('order/OrderDelivery')->getWhereInfo([
            'A.delivery_id' => $deliveryId,
        ]);

        if ($deliveryInfo['delivery_log_update'] + 21600 >= time()) {
            return $this->success($deliveryInfo['delivery_log']);
        }

        $orderConfig = target('order/OrderConfig')->getConfig();
        $waybillType = target('order/OrderConfigWaybill')->typeInfo($orderConfig['waybill_type']);

        if (empty($waybillType)) {
            return $this->error('物流查询接口不存在');
        }

        $expressInfo = target('order/OrderConfigExpress')->getWhereInfo([
            'name' => $deliveryInfo['delivery_name'],
        ]);

        if (empty($expressInfo)) {
            return $this->error('该配送类型不存在');
        }

        $target = target($waybillType['target'], 'waybill');
        $log = $target->query($deliveryInfo['delivery_name'], $expressInfo['label'], $deliveryInfo['delivery_no']);

        if (!$log) {
            return $this->error($target->getError());
        }

        target('order/OrderDelivery')->edit([
            'delivery_id' => $deliveryInfo['delivery_id'],
            'log' => serialize($log),
            'log_update' => time(),
        ]);

        return $this->success($log);
    }

    /**
     * 订单通知
     * @param $name
     * @param $orderId
     * @param array $data
     * @return bool
     */
    public function noticeOrder($name, $orderId, $data = []) {
        $config = target('order/orderConfig')->getConfig();

        $status = $config['notice_' . $name . '_status'];
        $class = unserialize($config['notice_' . $name . '_class']);
        $title = $config['notice_' . $name . '_title'];

        if (!$status) {
            return $this->error('通知类型未开启!');
        }

        if (empty($class) || empty($title)) {
            return $this->error('通知内容未设置完整!');
        }

        $orderInfo = target('order/Order')->getInfo($orderId);

        if (empty($orderInfo)) {
            return $this->error('该订单不存在！');
        }

        $userInfo = target('member/MemberUser')->getInfo($orderInfo['order_user_id']);

        $replaceData = [
            '用户昵称' => $userInfo['show_name'],
            '订单编号' => $orderInfo['order_no'],
            '订单标题' => $orderInfo['order_title'],
            '订单金额' => $orderInfo['order_price'],
            '付款金额' => $orderInfo['pay_price'],
            '下单时间' => date('Y-m-d H:i:s', $orderInfo['order_create_time']),
            '确认时间' => date('Y-m-d H:i:s'),
            '支付方式' => $orderInfo['pay_type'] ? '在线付款' : '货到付款',
            '快递费用' => $orderInfo['delivery_price'],
            '支付时间' => date('Y-m-d H:i:s'),
        ];

        $replaceData['发货时间'] = $data['delivery_time'];
        $replaceData['快递名称'] = $data['delivery_name'];
        $replaceData['快递单号'] = $data['delivery_no'];

        $replaceData['支付时间'] = date('Y-m-d H:i:s', $data['pay_time']);
        $replaceData['支付类型'] = $data['pay_name'];
        $replaceData['支付号'] = $data['pay_no'];
        $replaceData['支付金额'] = $data['pay_money'];

        if (LAYER_NAME == 'mobile') {
            $layer = 'mobile';
        } else {
            $layer = 'controller';
        }

        $url = url($layer . '/' . $orderInfo['order_app'] . '/' . 'order/info', ['order_no' => $orderInfo['order_no']], true);

        foreach ($class as $vo) {
            $content = html_out($config['notice_' . $name . '_' . $vo . '_tpl']);
            foreach ($replaceData as $key => $v) {
                $content = str_replace('[' . $key . ']', $v, $content);
            }

            $status = target('tools/Tools', 'service')->sendMessage([
                'receive' => $orderInfo['order_user_id'],
                'class' => $vo,
                'title' => $title,
                'content' => $content,
                'user_status' => 1,
                'param' => [
                    'url' => $url,
                    'ordertitle' => $orderInfo['order_title']
                ]
            ]);

            if (!$status) {
                return $this->error(target('tools/Tools', 'service')->getError());
            }
        }

        return $this->success();
    }

    /**
     * 订单状态
     * @param $info
     * @return string
     */
    public function getAction($info) {
        if (!$info['order_status']) {
            return [
                'name' => '已取消',
                'action' => 'close',
                'message' => '订单已取消，请重新下单'
            ];
        }

        if ($info['pay_type']) {
            if (!$info['pay_status']) {
                return [
                    'name' => '未付款',
                    'action' => 'pay',
                    'message' => '商品未付款，请及时进行支付',
                    'icon' => 'cny'
                ];
            }
        }

        if (!$info['parcel_status']) {
            return [
                'name' => '待配货',
                'action' => 'parcel',
                'message' => '订单等待商家配货中，请耐心等待',
                'icon' => 'cube'
            ];
        }

        if (!$info['delivery_status']) {
            return [
                'name' => '待发货',
                'action' => 'delivery',
                'message' => '订单正在发货中，请耐心等待',
                'icon' => 'cubes'
            ];
        }

        if ($info['order_complete_status']) {
            if (!$info['comment_status']) {
                return [
                    'name' => '待评价',
                    'action' => 'comment',
                    'message' => '订单已确认签收，请对商品进行评价',
                    'icon' => 'comment-o'
                ];
            } else {
                return [
                    'name' => '已完成',
                    'action' => 'complete',
                    'message' => '订单已完成，欢迎您下次光临',
                    'icon' => 'check'
                ];
            }
        } else {
            return [
                'name' => '待收货',
                'action' => 'receive',
                'message' => '订单已发货，请注意查收货品',
                'icon' => 'bus'
            ];
        }
    }


    /**
     * 获取订单操作HTML
     * @param $info
     * @return string
     */
    public function orderActionHtml($info, $tpl = '') {
        $btnData = [];
        switch ($info['status_data']['action']) {
            case 'pay':
                $btnData = [
                    'url' => url('order/pay/index', ['order_no' => $info['order_no']]),
                    'name' => '立即付款'
                ];
                break;
            case 'delivery':
                $btnData = [
                    'url' => url($info['order_app'] . '/order/info', ['order_no' => $info['order_no']]),
                    'name' => '待发货',
                ];
                break;
            case 'parcel':
                $btnData = [
                    'url' => url('order/order/cancel'),
                    'name' => '取消订单',
                    'confirm' => true
                ];
                break;
            case 'receive':
                $btnData = [
                    'url' => url('order/Order/confirm'),
                    'name' => '确认收货',
                    'confirm' => true
                ];
                break;
            case 'comment':
                $btnData = [
                    'url' => url($info['order_app'] . '/order/info', ['order_no' => $info['order_no']]),
                    'name' => '待评价'
                ];
                break;
            case 'complete':
                $btnData = [
                    'url' => url($info['order_app'] . '/order/info', ['order_no' => $info['order_no']]),
                    'name' => '已完成'
                ];
                break;
            case 'close':
                $btnData = [
                    'url' => url($info['order_app'] . '/order/info', ['order_no' => $info['order_no']]),
                    'name' => '已取消'
                ];
                break;
        }
        $btnData['order_no'] = $info['order_no'];

        if (empty($tpl)) {
            $btnHtml = '<a class="uk-button uk-button-small uk-button-default" [action] href="[href]">[name]</a>';
        } else {
            $btnHtml = $tpl;
        }
        if ($btnData['confirm']) {
            $btnData['action'] = 'data-dux="dialog-ajaxConfirm" data-url="[url]" data-title="您要确认[name]吗?" data-params=\'{"order_no" : "[order_no]"}\'';
            $btnData['href'] = 'javascript:;';
        } else {
            $btnData['href'] = $btnData['url'];
        }
        $btnHtml = str_replace('[action]', $btnData['action'], $btnHtml);
        foreach ($btnData as $key => $vo) {
            $btnHtml = str_replace('[' . $key . ']', $vo, $btnHtml);
        }
        return $btnHtml;
    }

    /**
     * 订单管理状态
     * @param $info
     * @return array
     */
    public function getManageStatus($info) {
        $data = [];

        if ($info['order_status'] && !$info['pay_status']) {
            $data['pay'] = true;
        }

        if ($info['order_status'] && ($info['pay_status'] || !$info['pay_type']) && !$info['parcel_status']) {
            $data['parcel'] = true;
        }

        if ($info['order_status'] && $info['parcel_status'] && !$info['delivery_status']) {
            $data['delivery'] = true;
        }

        if ($info['order_status'] && $info['delivery_status'] && !$info['order_complete_status']) {
            $data['complete'] = true;
        }

        if ($info['order_status'] && !$info['order_complete_status']) {
            $data['close'] = true;
        }

        return $data;
    }

    /**
     * 订单操作状态
     */
    public function getActionStatus($info, $goodsList) {
        $parcelInfo = target('order/OrderParcel')->getWhereInfo([
            'A.order_id' => $info['order_id']
        ]);
        foreach ($goodsList as $key => $vo) {
            if ((($info['status_data']['action'] == 'parcel' && $parcelInfo['status'] > 1) || $info['status_data']['action'] == 'delivery' || $info['status_data']['action'] == 'receive') && !$vo['service_status']) {
                $goodsList[$key]['action_service'] = true;
            }
            if (($info['status_data']['action'] == 'comment' && $parcelInfo['complete'] == 3) && !$vo['comment_status']) {
                $goodsList[$key]['action_comment'] = true;
            }
        }
        return $goodsList;

    }

    /**
     * 获取货币信息
     * @param $list
     * @return array
     */
    public function getCurrency($list) {
        $currencyType = target('member/MemberCurrency')->typeList();

        $exchangeMaxLimit = [];
        $exchangeMinLimit = [];
        $exchangeMoney = [];
        $currencyData = [];

        foreach ($list as $key => $val) {
            if (empty($val['currency'])) {
                continue;
            }
            $money = $val['currency']['money'] * $val['qty'];
            $currencyData[$val['currency']['type']] += $money;
            if (!$currencyType[$val['currency']['type']]['hybrid']) {
                $exchangeMoney[$val['currency']['type']] += $val['total'];
                $exchangeData[$val['currency']['type']] = $val['currency'];
                $exchangeMaxLimit[$val['currency']['type']] += $val['currency']['max_limit'] * $val['qty'];
                $exchangeMinLimit[$val['currency']['type']] += $val['currency']['min_limit'] * $val['qty'];
            }
        }

        $currencyAppend = [];
        $currencyExchange = [];
        foreach ($currencyData as $key => $vo) {

            if ($currencyType[$key]['hybrid']) {
                $currencyAppend[$key] = [
                    'type' => $key,
                    'name' => $currencyType[$key]['name'],
                    'unit' => $currencyType[$key]['unit'],
                    'money' => $vo,
                ];
            } else {
                $rate = $currencyType[$key]['rate'] ? $currencyType[$key]['rate'] : 1;
                $currencyExchange[$key] = [
                    'type' => $key,
                    'name' => $currencyType[$key]['name'],
                    'unit' => $currencyType[$key]['unit'],
                    'rate_money' => price_calculate(1,'/', $rate, 2), //兑换比例
                    'rate' => $rate,
                    'max_limit' => $exchangeMaxLimit[$key], //最大使用
                    'min_limit' => $exchangeMinLimit[$key], //最小使用
                    'money' => 0
                ];

                $curCurrency = price_calculate($exchangeMoney[$key],'/', $rate, 2);
                if($curCurrency < $currencyExchange[$key]['max_limit']) {
                    $currencyExchange[$key]['max_limit'] = $curCurrency;
                }elseif(empty($currencyExchange[$key]['max_limit'])) {
                    $currencyExchange[$key]['max_limit'] = $curCurrency;
                }
            }
        }

        return [
            'append' => $currencyAppend,
            'exchange' => $currencyExchange
        ];
    }


    /**
     * 拆分订单
     * @param string $area
     * @param array $data
     * @return array
     */
    public function splitOrder($area = '', $data = []) {
        if (empty($data)) {
            return [];
        }

        $appData = [];
        $orderData = [];
        foreach ($data as $key => $vo) {
            $appData[$vo['app']][] = $vo;
        }

        foreach ($appData as $app => $vo) {
            $priceData = $this->priceData($vo, $area);

            $invoice = 0;
            foreach ($vo as $k => $v) {
                if($invoice) {
                    break;
                }
                if($v['invoice_status']) {
                    $invoice = 1;
                }
            }
            $orderData[] = array_merge($priceData, ['app' => $app, 'invoice_status' => $invoice, 'items' => $vo]);
        }

        $hookList = run('service', 'order', 'splitOrder', [$orderData]);

        foreach ($orderData as $key => $vo) {
            $ids = [];
            foreach ($vo['items'] as $v) {
                $ids[] = $v['id'];
            }
            $orderData[$key]['ids'] = implode(',', $ids);
        }

        if (!empty($hookList)) {
            $hookData = [];
            foreach ($hookList as $app => $vo) {
                $hookData = array_merge($hookData, $vo);
            }
            $orderData = $hookData ? $hookData : $orderData;
        }

        return $orderData;
    }

    /**
     * 价格计算
     * @param $data
     * @param $area
     * @return array
     */
    public function priceData($data, $area) {
        $weightData = [];
        $tplIds = [];
        $freightPrice = 0;
        $orderPrice = 0;
        $orderCount = 0;

        foreach ($data as $vo) {
            if ($vo['freight_type']) {
                //模板运费
                $tplIds[] = $vo['freight_tpl'];
                $weightData[$vo['freight_tpl']] += $vo['weight'] * $vo['qty'];
            } else {
                //固定运费
                $freightPrice = price_calculate($freightPrice, '+', $vo['freight_price']);
            }
            $orderCount += $vo['qty'];
            $orderPrice = price_calculate($orderPrice, '+', $vo['price'] * $vo['qty']);
        }

        $tplIds = array_unique($tplIds);
        $tplList = [];

        if ($tplIds) {
            $tplList = target('order/OrderConfigDelivery')->loadList([
                '_sql' => 'delivery_id in (' . implode(',', $tplIds) . ')',
            ]);
        }

        foreach ($tplList as $key => $vo) {
            $areaList = unserialize($vo['area']);

            if (!empty($areaList)) {
                foreach ($areaList as $v) {
                    $areaData = explode(',', $v['area']);
                    if (in_array($area, $areaData)) {
                        $vo['first_price'] = $v['first_price'];
                        $vo['second_price'] = $v['second_price'];
                    }
                }
            }
            $freightPrice = price_calculate($freightPrice, '+', $vo['first_price']);
            $orderPrice = price_calculate($orderPrice, '+', $vo['price'] * $vo['qty']);

            $weight = $weightData[$vo['tpl_id']];

            if ($weight <= 0) {
                continue;
            }

            if ($weight < $vo['first_weight']) {
                continue;
            }

            $secondWeight = $weight - $vo['first_weight'];
            $secondWeight = $vo['second_weight'] ? ceil($secondWeight / $vo['second_weight']) : 0;
            $freightPrice += $secondWeight * $vo['second_price'];
            $freightPrice = price_calculate($freightPrice, '*', $vo['second_price']);
        }

        return [
            'order_price' => $orderPrice,
            'delivery_price' => $freightPrice,
            'order_sum' => $orderCount,
        ];
    }
}
