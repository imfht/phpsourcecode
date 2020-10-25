<?php
namespace app\sale\service;
/**
 * 订单操作
 */
class OrderService extends \app\base\service\BaseService {

    public $saleConfig = [];

    public function __construct() {
        $this->saleConfig = target('sale/SaleConfig')->getConfig();
    }

    /**
     * 付款成功回调
     * @param $orderList
     * @return bool
     */
    public function hookPayOrder($orderList) {
        if(!$this->saleConfig['sale_status']) {
            return true;
        }

        $saleData = [];
        $moneyData = [];

        foreach ($orderList as $order) {
            $userInfo = target('sale/SaleUser')->getWhereInfo([
                'A.user_id' => $order['order_user_id']
            ]);
            if (empty($userInfo)) {
                continue;
            }
            $levelInfo = target('sale/SaleUserLevel')->getInfo($userInfo['level_id']);
            $commRate = unserialize($levelInfo['comm_rate']);
            if (empty($commRate)) {
                continue;
            }

            $parentList = target('sale/saleUser')->loadParentList($order['order_user_id'], $this->saleConfig['sale_purchase'] ? 0 : 1, $this->saleConfig['sale_level']);
            if (empty($parentList)) {
                continue;
            }

            //商品
            $orderGoods = target('order/OrderGoods')->loadHasList([
                'A.order_id' => $order['order_id']
            ]);

            //被动接口
            $profits = 0;
            $hookList = run('service', 'Sale', 'profits', [$order]);
            if (!empty($hookList)) {
                foreach ($hookList as $a => $vo) {
                    $profits = $vo;
                }
            }
            $orderSale = 0;
            foreach ($orderGoods as $goods) {
                $info = target('sale/SaleContent')->getWhereInfo([
                    'app' => $order['order_app'],
                    'has_id' => $goods['has_id']
                ]);
                if (empty($info)) {
                    continue;
                }
                if (!$info['sale_status']) {
                    continue;
                }

                if ($info['sale_special']) {
                    $goodsRate = unserialize($info['sale_rate']);
                } else {
                    $goodsRate = $commRate;
                }
                if (empty($goodsRate)) {
                    continue;
                }

                $i = -1;
                foreach ($goodsRate as $key => $vo) {
                    $i++;
                    if (empty($parentList[$i])) {
                        continue;
                    }
                    if ($vo['rate'] && $vo['money']) {
                        continue;
                    }
                    $money = 0;
                    if ($vo['rate']) {
                        $money = round($goods['price_total'] * $vo['rate'] / 100, 2);
                    } elseif ($vo['money']) {
                        $money = $vo['money'];
                    }

                    if ($money <= 0) {
                        continue;
                    }

                    if($profits) {
                        $money = round($money * $profits / 100, 2);
                    }

                    if ($money <= 0) {
                        continue;
                    }

                    $moneyData[$parentList[$i]['user_id']] += $money;
                    $orderSale += $money;

                    $saleData[] = [
                        'user_id' => $parentList[$i]['user_id'],
                        'order_goods_id' => $goods['id'],
                        'sale_status' => 1,
                        'create_time' => time(),
                        'level' => $key,
                        'sale_money' => $money
                    ];
                }
            }

            $i = 0;
            foreach ($parentList as $vo) {
                $i++;
                target('sale/Sale', 'service')->noticeSale('pay', $vo['user_id'], [
                    '昵称' => $userInfo['show_name'],
                    '时间' => date('Y-m-d H:i', time()),
                    '订单编号' => $order['order_no'],
                    '订单金额' => $order['order_price'],
                    '订单名称' => $order['order_title'],
                    '佣金金额' => $orderSale,
                    '下线层级' => $i
                ]);
            }

        }

        if (empty($saleData)) {
            return true;
        }

        //记录推广订单
        foreach ($saleData as $vo) {
            if (!target('sale/SaleOrder')->add($vo)) {
                return $this->error('推广订单处理失败！');
            }
        }

        return true;
    }


    /**
     * 订单完成
     * @param $orderInfo
     * @return bool
     */
    public function hookConfirmOrder($orderInfo) {
        $orderGoods = target('order/OrderGoods')->loadList([
            'order_id' => $orderInfo['order_id']
        ]);

        $ids = [];
        foreach ($orderGoods as $vo) {
            $ids[] = $vo['id'];
        }
        if (empty($ids)) {
            return true;
        }

        $orderList = target('sale/SaleOrder')->loadList([
            '_sql' => 'A.order_goods_id in (' . implode(',', $ids) . ')'
        ]);

        if (empty($orderList)) {
            return true;
        }

        $moneyData = [];
        $levelData = [];
        $ids = [];
        foreach ($orderList as $vo) {
            if ($vo['sale_status'] <> 1) {
                continue;
            }
            $ids[] = $vo['id'];
            $moneyData[$vo['user_id']] += $vo['sale_money'];
            $levelData[$vo['user_id']] = $vo['level'];
        }

        if (empty($ids)) {
            return true;
        }

        $userInfo = target('sale/saleUser')->getWhereInfo(['A.user_id' => $orderInfo['order_user_id']]);

        foreach ($moneyData as $userId => $money) {
            target('sale/SaleStatis')->updateOrder($userInfo['user_id'], $money);

            if (!target('sale/SaleUser')->where(['user_id' => $userId])->setInc('money', $money)) {
                return $this->error('佣金处理失败！');
            }
            target('sale/Sale', 'service')->noticeSale('confirm', $userId, [
                '昵称' => $userInfo['show_name'],
                '时间' => date('Y-m-d H:i', time()),
                '订单编号' => $orderInfo['order_no'],
                '下线层级' => $levelData[$userId],
                '订单金额' => $orderInfo['order_price'],
                '订单名称' => $orderInfo['order_title'],
                '佣金金额' => $money,
            ]);

            $status = target('member/Finance', 'service')->account([
                'user_id' => $userId,
                'money' => $money,
                'pay_name' => '系统支付',
                'type' => 1,
                'deduct' => 1,
                'title' => '销售奖励',
                'remark' => $levelData[$userId] . '级订单完成返利'
            ]);
            if (!$status) {
                $this->error(target('member/Finance', 'service')->getError());
            }
            if (!target('sale/Sale', 'service')->levelUser($userId)) {
                return $this->error('等级处理失败！');
            }
        }

        if (target('sale/SaleOrder')->where(['_sql' => 'id in (' . implode(',', $ids) . ')'])->data(['sale_status' => 2, 'complete_time' => time()])->update() === false) {
            return $this->error('佣金处理失败！');
        }



        return true;
    }

    /**
     * 订单取消
     * @param $orderData
     * @return bool
     */
    public function hookCancelOrder($orderData) {
        if (empty($orderData)) {
            return true;
        }

        $ids = [];
        foreach ($orderData as $vo) {
			$ids[] = $vo['order_id'];
        }
        if (empty($ids)) {
            return true;
        }


        $orderGoods = target('order/OrderGoods')->loadList([
            '_sql' => 'order_id in (' . implode(',', $ids) . ')'
        ]);

        $ids = [];
        foreach ($orderGoods as $vo) {
            $ids[] = $vo['id'];
        }
        if (empty($ids)) {
            return true;
        }

        $orderList = target('sale/SaleOrder')->loadList([
            '_sql' => 'A.order_goods_id in (' . implode(',', $ids) . ')'
        ]);

        if (empty($orderList)) {
            return true;
        }

        $moneyData = [];
        $ids = [];
        foreach ($orderList as $vo) {
            if ($vo['sale_status'] <> 1) {
                continue;
            }
            $ids[] = $vo['id'];
            $moneyData[$vo['user_id']] += $vo['sale_money'];
        }

        if (empty($ids)) {
            return true;
        }

        if (target('sale/SaleOrder')->where(['_sql' => 'id in (' . implode(',', $ids) . ')'])->data(['sale_status' => 0])->update() === false) {
            return $this->error('佣金处理失败！');
        }

        return true;


    }
}

