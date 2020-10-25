<?php
namespace app\mall\service;
/**
 * 操作
 */
class MallService extends \app\base\service\BaseService {


    //获取购物车数据
    public function getCart($userId, $hasId = 0, $subId = 0, $qty = 1) {
        if ((empty($subId) || empty($hasId)) && empty($userId)) {
            return $this->error('商品参数有误');
        }
        if ($hasId && empty($subId)) {
            $proInfo = target('mall/MallProducts')->getMallInfo(['A.mall_id' => $hasId]);
        } else {
            $proInfo = target('mall/MallProducts')->getMallInfo(['A.products_id' => $subId]);
        }

        if (empty($proInfo) || !$proInfo['status']) {
            return $this->error('该商品不存在或已下架');
        }
        if ($proInfo['up_time']) {
            if ($proInfo['up_time'] > time()) {
                return $this->error('该商品还未上架');
            }
        }
        if ($proInfo['down_time']) {
            if ($proInfo['down_time'] < time()) {
                return $this->error('该商品已下架!');
            }
        }
        if (empty($proInfo['store'])) {
            return $this->error('该商品已售完！');
        }

        if ($proInfo['store'] < $qty) {
            return $this->error('该商品已售完!');
        }

        $info = target('mall/Mall')->getInfo($proInfo['mall_id']);

        if ($info['purchase_limit']) {
            $goodsWhere = [
                'A.user_id' => $userId,
                'A.has_id' => $proInfo['mall_id'],
                'B.order_status' => 1,
            ];
            if($info['purchase_type'] && $info['purchase_day']) {
                $goodsWhere['_sql'] = 'B.order_create_time >= ' . (time() - $info['purchase_day'] * 86400);
            }
            $orderGoods = target('order/OrderGoods')->loadHasList($goodsWhere);
            $goodsNum = $qty;
            foreach ($orderGoods as $vo) {
                $goodsNum += $vo['goods_qty'];
            }
            if ($goodsNum > $info['purchase_limit']) {
                if($info['purchase_type'] && $info['purchase_day']) {
                    return $this->error('该商品'.$info['purchase_day'].'天内限购' . $info['purchase_limit'] . $info['unit'] . '!');
                }else {
                    return $this->error('该商品限购' . $info['purchase_limit'] . $info['unit'] . '!');
                }
            }
        }

        $cartData = [];
        $cartData['item_no'] = $proInfo['products_no'];
        $cartData['app'] = 'mall';
        $cartData['app_id'] = $proInfo['mall_id'];
        $cartData['id'] = $proInfo['products_id'];
        $cartData['qty'] = $qty;
        $cartData['price'] = $proInfo['sell_price'];
        $cartData['cost_price'] = $proInfo['cost_price'];
        $cartData['market_price'] = $proInfo['market_price'];
        $cartData['name'] = $proInfo['title'];
        $cartData['options'] = $proInfo['spec_data'];
        $cartData['image'] = $proInfo['image'];
        $cartData['weight'] = $proInfo['weight'];
        $cartData['url'] = url(VIEW_LAYER_NAME . '/mall/info/index', ['id' => $proInfo['mall_id']]);
        $cartData['freight_type'] = $proInfo['freight_type'];
        $cartData['freight_tpl'] = $proInfo['freight_tpl'];
        $cartData['freight_price'] = $proInfo['freight_price'];
        $cartData['service_status'] = $proInfo['service_status'];
        $cartData['invoice_status'] = $proInfo['invoice_status'];
        $cartData['cod_status'] = $proInfo['cod_status'];
        $cartData['point'] = $proInfo['point_status'];

        return $this->success($cartData);
    }
}

