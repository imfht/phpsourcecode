<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/18
 * Time: 下午4:03
 */

namespace App\Services;

use App\Models\Cart;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\MarketSeckill;
use App\Models\OrderInvoice;
use App\Models\Promotion;
use App\Models\Seller;
use Illuminate\Support\Facades\Redis;

class GoodsService
{

    /**
     * 解析商品规格
     * @param $value
     * @return string
     */
    public static function formatSpecValue($value)
    {
        $spec_str = '';
        $spec_value = json_decode($value, true);
        foreach ($spec_value as $value) {
            $spec_str .= $value['name'] . ':' . $value['alias'] . '+';
        }
        return trim($spec_str, '+');
    }

    /**
     * 获取购物车信息
     * @param $cart
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function formatCart(array $cart)
    {
        $goods = self::formatSellerGoods($cart);
        if ($goods['valid_goods']) {
            //获取当前店铺的优惠活动
            $goods['valid_goods'] = self::promotionList($goods['valid_goods']);
        }
        $goods['valid_goods'] = self::eliminateUselessParams($goods['valid_goods']);//剔除沉余参数
        $goods['invalid_goods'] = self::eliminateUselessParams($goods['invalid_goods']);//剔除沉余参数
        return $goods;
    }

    /**
     * 计算选中商品的金额、优惠、运费
     * @param $goods
     * @param $prov_id
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function getConfirm(array $goods, int $prov_id)
    {
        if (!$goods['valid_goods'] || $goods['invalid_goods']) {
            api_error(__('api.goods_is_update'));
        }
        $format_goods = $goods['valid_goods'];
        //过滤库存不足、数量错误的商品
        self::checkErrorGoods($format_goods);
        $format_goods = self::sumSellerGoodsPrice($format_goods);
        //获取商品优惠券
        $format_goods = self::getCoupons($format_goods);
        //计算商品优惠信息
        $format_goods = self::promotionPrice($format_goods);
        //获取商品邮费
        $format_goods = self::getDeliveryPrice($format_goods, $prov_id);
        $format_goods = self::eliminateUselessParams($format_goods);//剔除沉余参数
        return $format_goods;
    }

    /**
     * 计算选中商品的金额、优惠、运费
     * @param $goods
     * @param $prov_id
     * @param $conpons
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function getOrderPrice(array $goods, int $prov_id, $conpons = array())
    {
        if (!$goods['valid_goods'] || $goods['invalid_goods']) {
            api_error(__('api.goods_is_update'));
        }
        $format_goods = $goods['valid_goods'];
        //过滤库存不足、数量错误的商品
        self::checkErrorGoods($format_goods);
        $format_goods = self::sumSellerGoodsPrice($format_goods);
        //计算优惠券优惠金额
        if ($conpons) {
            $format_goods = self::checkCoupons($format_goods, $conpons);
        }
        //计算促销优惠信息
        $format_goods = self::promotionPrice($format_goods);
        //获取商品邮费
        $format_goods = self::getDeliveryPrice($format_goods, $prov_id);
        return $format_goods;
    }

    /**
     * 计算订单总金额
     * @param $format_goods
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function sumOrderPrice(array $format_goods)
    {
        $subtotal = $sell_price = $delivery_price = $promotion_price = 0;
        foreach ($format_goods as $seller_goods) {
            $subtotal += $seller_goods['price']['subtotal'];
            $sell_price += $seller_goods['price']['sell_price'];
            $promotion_price += $seller_goods['price']['promotion_price'];
            $delivery_price += $seller_goods['delivery']['delivery_price_real'];
        }
        $return = array(
            'sell_price' => $sell_price,
            'delivery_price' => $delivery_price,
            'promotion_price' => $promotion_price,
            'subtotal' => $subtotal,
        );
        return $return;
    }

    /**
     * 将商品按商家分类，并区分出已经失效的
     * @param $cart
     * @param $type 下单类型
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function formatSellerGoods(array $cart, $type = false)
    {
        if (!is_array($cart) && !$cart) return array();
        $sku_ids = array_keys($cart);
        $is_special = ($type == Cart::TYPE_SECKILL) ? true : false;
        $format_goods_sku = self::formatGoodsSku($sku_ids, $is_special);

        $seller_ids = array();
        foreach ($format_goods_sku as $value) {
            $seller_ids[] = $value['seller_id'];
        }
        //获取商家信息
        $res_seller = Seller::whereIn('id', array_unique($seller_ids))
            ->select('id', 'title', 'image', 'status', 'invoice')
            ->get();

        if ($res_seller->isEmpty()) {
            api_error(__('api.seller_error'));
        }
        $res_seller = array_column($res_seller->toArray(), null, 'id');

        $format_goods_sku = array_column($format_goods_sku, null, 'sku_id');
        $new_goods_sku = array();
        //将商品按加入购物车的时间排序
        foreach ($cart as $key => $value) {
            $new_goods_sku[$key] = array_merge($format_goods_sku[$key], ['buy_qty' => $value]);
        }

        //组装商品和商家信息，并区分失效商品
        $valid_goods = $invalid_goods = array();
        foreach ($new_goods_sku as $sku) {
            $seller_id = $sku['seller_id'];
            $_seller = isset($res_seller[$seller_id]) ? $res_seller[$seller_id] : array();
            //判断商品和商家的状态
            if ($_seller['status'] != Seller::STATUS_ON || $sku['status'] != GoodsSku::STATUS_ON || $sku['shelves_status'] != Goods::SHELVES_STATUS_ON) {
                $invalid_goods[$seller_id]['seller'] = $_seller;
                $invalid_goods[$seller_id]['goods'][] = $sku;
            } else {
                //一下的判断主要是提供给购物车提示使用
                //判断库存
                if ($sku['min_buy'] > $sku['buy_qty']) {
                    $sku['error_tip'] = __('api.tip_goods_min_buy_qty_error') . $sku['min_buy'];
                }
                if ($sku['buy_qty'] > $sku['stock']) {
                    $sku['error_tip'] = __('api.tip_goods_stock_no_enough');
                }

                $valid_goods[$seller_id]['seller'] = $_seller;
                $valid_goods[$seller_id]['goods'][] = $sku;
            }
        }
        $return = array(
            'valid_goods' => array_values($valid_goods),
            'invalid_goods' => array_values($invalid_goods)
        );

        return $return;

    }

    /**
     * 根据sku_id获取商品信息
     * @param $sku_ids
     * @param $is_special 是否活动商品
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function formatGoodsSku(array $sku_ids, $is_special = false)
    {
        if (!is_array($sku_ids) && !$sku_ids) return array();
        //获取子商品信息
        $res_sku = GoodsSku::whereIn('id', $sku_ids)
            ->select('id as sku_id', 'goods_id', 'image', 'sku_code', 'spec_value', 'stock', 'sell_price', 'market_price', 'point', 'weight', 'min_buy', 'status', 'activity_stock', 'activity_price')
            ->get();
        if ($res_sku->isEmpty()) {
            api_error(__('api.goods_sku_error'));
        }
        $goods_ids = array();
        foreach ($res_sku as $value) {
            $goods_ids[] = $value['goods_id'];
        }
        //获取主商品信息
        $res_goods = Goods::whereIn('id', array_unique($goods_ids))
            ->select('id as goods_id', 'title', 'seller_id', 'brand_id', 'category_id', 'delivery_id', 'shelves_status', 'market_type', 'market_id', 'level_one_pct', 'level_two_pct')
            ->get();
        if ($res_goods->isEmpty()) {
            api_error(__('api.goods_error'));
        }
        $res_goods = array_column($res_goods->toArray(), null, 'goods_id');
        $sku = array();
        foreach ($res_sku->toArray() as $value) {
            $_goods = isset($res_goods[$value['goods_id']]) ? $res_goods[$value['goods_id']] : array();
            //获取活动价格和会员折扣
            $value = self::getVipPrice($value, $is_special);
            $_sku = array_merge($value, $_goods);
            $_sku['spec_value'] = self::formatSpecValue($value['spec_value']);
            $_sku['promotion_price'] = 0;//优惠金额
            $sku[] = $_sku;
        }
        return $sku;
    }

    /**
     * 检测秒杀商品的限制条件
     * @param int $goods_id 主商品id
     * @param int $sku_id 商品skuid
     * @param int $buy_qty 购买数量
     * @param bool $stock_decr 是否扣减库存
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function checkMarketSeckill(int $goods_id, int $sku_id, int $buy_qty, $stock_decr = false)
    {
        if (!$goods_id || !$sku_id || !$buy_qty) {
            api_error(__('api.missing_params'));
        }
        //验证库存
        $_redis_key = MarketSeckill::GOODS_REDIS_KEY . $goods_id;
        $stock = Redis::hget($_redis_key, $sku_id);
        if ($stock < $buy_qty) {
            api_error(__('api.goods_stock_no_enough'));//秒杀库存不足
        }
        //开始减去库存
        if ($stock_decr) {
            Redis::hincrby($_redis_key, $sku_id, -$buy_qty);
        }
        $goods = Goods::where('id', $goods_id)->first();
        if ($goods['market_id'] && $goods['market_type']) {
            //验证秒杀信息
            $seckill = MarketSeckill::select('id', 'start_at', 'end_at', 'status', 'goods_id')->where('id', $goods['market_id'])->first();
            if (!$seckill) {
                api_error(__('api.seckill_goods_error'));
            }
            if ($seckill['start_at'] > get_date()) {
                api_error(__('api.seckill_not_start'));
            } elseif ($seckill['end_at'] < get_date()) {
                api_error(__('api.seckill_is_end'));
            } elseif ($seckill['status'] != MarketSeckill::STATUS_ON) {
                api_error(__('api.seckill_status_error'));
            }
        } else {
            api_error(__('api.seckill_goods_error'));
        }
    }

    /**
     * 剔除沉余字段信息
     * @param $format_goods
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function eliminateUselessParams(array $format_goods)
    {
        if (!$format_goods) return array();
        foreach ($format_goods as $seller_id => $seller_goods) {
            unset($format_goods[$seller_id]['seller']['status'], $format_goods[$seller_id]['delivery']['sku_ids']);
            foreach ($seller_goods['goods'] as $sku_id => $goods) {
                unset($format_goods[$seller_id]['goods'][$sku_id]['weight'],
                    $format_goods[$seller_id]['goods'][$sku_id]['status'],
                    $format_goods[$seller_id]['goods'][$sku_id]['seller_id'],
                    $format_goods[$seller_id]['goods'][$sku_id]['brand_id'],
                    $format_goods[$seller_id]['goods'][$sku_id]['category_id'],
                    $format_goods[$seller_id]['goods'][$sku_id]['delivery_id'],
                    $format_goods[$seller_id]['goods'][$sku_id]['shelves_status']
                );
            }
        }
        return $format_goods;
    }

    /**
     * 计算的价格件数
     * @param array $format_goods
     * @return array
     */
    public static function sumSellerGoodsPrice(array $format_goods)
    {
        if (!$format_goods) return array();
        foreach ($format_goods as $seller_id => $seller_goods) {
            $price = self::sumGoodsPrice($seller_goods['goods']);
            $all_buy_qty = self::sumGoodsBuyQty($seller_goods['goods']);
            $format_goods[$seller_id]['price'] = $price;
            $format_goods[$seller_id]['all_buy_qty'] = $all_buy_qty;
        }
        return $format_goods;
    }

    /**
     * 计算商品的价格
     * @param $seller_goods
     * @return array
     */
    public static function sumGoodsPrice(array $seller_goods)
    {
        if (!$seller_goods) return array();
        $all_sell_price = $all_market_price = $all_weight = $all_point = $all_promotion_price = 0;
        foreach ($seller_goods as $goods) {
            $_sell_price = $goods['show_price'] * $goods['buy_qty'];
            $_market_price = $goods['line_price'] * $goods['buy_qty'];
            $_weight = $goods['weight'] * $goods['buy_qty'];
            $_point = $goods['point'] * $goods['buy_qty'];
            $all_promotion_price += $goods['promotion_price'];
            $all_sell_price += $_sell_price;
            $all_market_price += $_market_price;
            $all_weight += $_weight;
            $all_point += $_point;
        }

        $price = array(
            'sell_price' => format_price($all_sell_price),
            'market_price' => format_price($all_market_price),
            'weight' => format_price($all_weight),
            'point' => format_price($all_point),
            'promotion_price' => format_price($all_promotion_price),//优惠金额
            'subtotal' => format_price($all_sell_price - $all_promotion_price)//需要支付金额
        );

        return $price;
    }

    /**
     * 计算购买商品的件数
     * @param $seller_goods
     * @return array
     */
    public static function sumGoodsBuyQty(array $seller_goods)
    {
        if (!$seller_goods) return array();
        $all_buy_qty = 0;
        foreach ($seller_goods as $goods) {
            $all_buy_qty += $goods['buy_qty'];
        }
        return $all_buy_qty;
    }

    /**
     * 获取商品的优惠活动
     * @param $format_goods
     * @return array
     */
    public static function promotionList(array $format_goods)
    {
        if (!$format_goods) return array();
        $group = get_user_group();
        $group_id = $group['group_id'];
        //$format_goods = self::sumGoodsPrice($format_goods);//如果需要提示还差多少才能参与活动开启这里就可以计算当前金额
        foreach ($format_goods as $seller_id => $seller_goods) {
            //查询该商家下的优惠活动
            $where = array(
                ['seller_id', $seller_goods['seller']['id']],
                ['status', Promotion::STATUS_ON],
                ['start_at', '<=', get_date()],
                ['end_at', '>=', get_date()],
            );
            $res_promotion = Promotion::select('title', 'use_price')->where($where)->whereRaw("find_in_set($group_id, user_group)")->get();
            if (!$res_promotion->isEmpty()) {
                //促销活动列表
                $format_goods[$seller_id]['promotion'] = $res_promotion->toArray();
            }
        }
        return $format_goods;
    }

    /**
     * 获取商品的优惠金额
     * @param $format_goods
     * @return array
     */
    public static function promotionPrice(array $format_goods)
    {
        if (!$format_goods) return array();
        $group = get_user_group();
        $group_id = $group['group_id'];
        foreach ($format_goods as $seller_id => $seller_goods) {
            //查询该商家下的优惠活动
            $where = array(
                ['seller_id', $seller_goods['seller']['id']],
                ['status', Promotion::STATUS_ON],
                ['use_price', '<=', $seller_goods['price']['subtotal']],
                ['start_at', '<=', get_date()],
                ['end_at', '>=', get_date()],
            );
            $type_promotion = array(Promotion::TYPE_REDUCTION, Promotion::TYPE_DISCOUNT);
            $res_promotion = Promotion::select('title', 'type', 'type_value')->where($where)->whereIn('type', $type_promotion)->whereRaw("find_in_set($group_id, user_group)")->get();
            if (!$res_promotion->isEmpty()) {
                $promotion_title = '';
                $promotion_price = 0;
                $subtotal = $seller_goods['price']['subtotal'];
                foreach ($res_promotion as $value) {
                    switch ($value['type']) {
                        case Promotion::TYPE_REDUCTION:
                            $new_promotion_price = $value['type_value'];//优惠金额
                            break;
                        case Promotion::TYPE_DISCOUNT:
                            if ($value['type_value']) {
                                $new_promotion_price = $subtotal - ($subtotal * ($value['type_value'] / 100));//优惠金额
                            }
                            break;
                    }
                    //获取优惠最大的活动
                    $new_promotion_price = format_price($new_promotion_price);
                    if ($promotion_price < $new_promotion_price) {
                        $promotion_price = $new_promotion_price;
                        $promotion_title = $value['title'];
                    }
                }
                $format_goods[$seller_id]['price']['promotion_price'] += $promotion_price;
                $format_goods[$seller_id]['price']['subtotal'] = $subtotal - $promotion_price;
                $format_goods[$seller_id]['promotion'][] = array('title' => $promotion_title, 'price' => $promotion_price);

                //开始平均分摊优惠金额
                $format_goods[$seller_id]['goods'] = self::getPromotionRate($seller_goods['goods'], $subtotal, $promotion_price);
            }
        }
        return $format_goods;
    }

    /**
     * 提交订单检测商品库存购买数量（后期还可以检测是否满足优惠等）
     * @param $format_goods
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public static function checkErrorGoods(array $format_goods)
    {
        if (!$format_goods) return array();
        //验证库存和购买数量
        foreach ($format_goods as $seller_goods) {
            foreach ($seller_goods['goods'] as $goods) {
                //判断库存
                if ($goods['min_buy'] > $goods['buy_qty']) {
                    api_error(__('api.goods_min_buy_qty_error'));
                }
                if ($goods['buy_qty'] > $goods['stock']) {
                    api_error(__('api.goods_stock_no_enough'));
                }
            }
        }
    }

    /**
     * 计算优惠金额所占权重
     * @param $format_goods 参与的商品
     * @param $subtotal 总金额
     * @param $promotion_price 优惠金额
     * @return array
     */
    public static function getPromotionRate(array $seller_goods, $subtotal, $promotion_price)
    {
        if (!$seller_goods) return array();
        if (!$promotion_price) return $seller_goods;

        //计算比例
        $tmp_total_rate = 0;
        $tmp_rate = array();
        foreach ($seller_goods as $goods_id => $goods) {
            $pct = round(($goods['show_price'] * $goods['buy_qty'] - $goods['promotion_price']) / $subtotal, 4);
            $tmp_total_rate += $pct;
            $tmp_rate[$goods_id] = $pct;
        }
        //在比例加起来不等于1的时候需要容差
        if ($tmp_total_rate != 1) {
            $_rate = $tmp_total_rate - 1;
            if ($_rate < 1) {
                $_rate = abs($_rate);
                $goods_key = array_search(min($tmp_rate), $tmp_rate);
            } else {
                $_rate = -abs($_rate);
                $goods_key = array_search(max($tmp_rate), $tmp_rate);
            }
            $tmp_rate[$goods_key] += $_rate;
        }

        //计算商品的优惠金额
        $tmp_total_promotion_price = 0;
        $tmp_promotion_price_arr = array();
        foreach ($seller_goods as $goods_id => $goods) {
            $_promotion_price = format_price($tmp_rate[$goods_id] * $promotion_price);
            $tmp_total_promotion_price += $_promotion_price;
            $tmp_promotion_price_arr[$goods_id] = $_promotion_price;
            $seller_goods[$goods_id]['promotion_price'] += $_promotion_price;
        }
        //在总优惠金额加起来不等于优惠金额的时候需要容差
        if ($tmp_total_promotion_price != $promotion_price) {
            $_price = $tmp_total_promotion_price - $promotion_price;
            if ($_price < $promotion_price) {
                $_price = abs($_price);
                $goods_key = array_search(min($tmp_promotion_price_arr), $tmp_promotion_price_arr);
            } else {
                $_price = -abs($_price);
                $goods_key = array_search(max($tmp_promotion_price_arr), $tmp_promotion_price_arr);
            }
            $seller_goods[$goods_key]['promotion_price'] += format_price($_price);
        }
        return $seller_goods;
    }

    /**
     * 获取邮费信息
     * @param $format_goods
     * @param $prov_id
     * @return array
     */
    public static function getDeliveryPrice(array $format_goods, int $prov_id)
    {
        if (!$format_goods) return array();
        foreach ($format_goods as $seller_id => $seller_goods) {
            $delivery_price = DeliveryService::getPrice($seller_goods['seller']['id'], $seller_goods['goods'], $prov_id);
            if ($delivery_price['sku_ids']) {
                //存在不可配送的商品时候
                $new_seller_goods = array();
                foreach ($seller_goods['goods'] as $goods) {
                    $_goods = $goods;
                    if (in_array($goods['sku_id'], $delivery_price['sku_ids'])) {
                        $_goods['is_delivery'] = __('api.delivery_can_not');//不在配送范围内
                    }
                    $new_seller_goods[] = $_goods;
                }
                $format_goods[$seller_id]['goods'] = $new_seller_goods;
            }
            $subtotal = $format_goods[$seller_id]['price']['subtotal'] + $delivery_price['delivery_price_real'];
            $format_goods[$seller_id]['price']['subtotal'] = format_price($subtotal);
            $format_goods[$seller_id]['delivery'] = $delivery_price;
        }
        return $format_goods;
    }

    /**
     * 获取优惠券信息
     * @param $format_goods
     * @return array
     */
    public static function getCoupons(array $format_goods)
    {
        if (!$format_goods) return array();
        foreach ($format_goods as $seller_id => $seller_goods) {
            $coupons = CouponsService::getCoupons($seller_goods['seller']['id'], $seller_goods['goods']);
            $format_goods[$seller_id]['coupons'] = $coupons;
        }
        return $format_goods;
    }

    /**
     * 检测优惠券信息
     * @param $format_goods
     * @param $coupons
     * @return array
     */
    public static function checkCoupons(array $format_goods, array $coupons)
    {
        if (!$format_goods) return array();
        if (!$coupons) return $format_goods;
        foreach ($format_goods as $seller_id => $seller_goods) {
            $coupons_id = isset($coupons[$seller_goods['seller']['id']]) ? $coupons[$seller_goods['seller']['id']] : 0;
            if ($coupons_id) {
                $coupons_data = CouponsService::checkCoupons($seller_goods['seller']['id'], $coupons_id, $seller_goods['goods']);
                if ($coupons_data) {
                    $sku_ids = $coupons_data['sku_id'];
                    //过滤掉不符合的商品
                    $coupons_goods = array();
                    foreach ($seller_goods['goods'] as $goods) {
                        if (in_array($goods['sku_id'], $sku_ids)) {
                            $coupons_goods[] = $goods;
                        }
                    }
                    $price = self::sumGoodsPrice($coupons_goods);
                    //计算优惠金额所占权重
                    $promotion_goods = self::getPromotionRate($coupons_goods, $price['subtotal'], $coupons_data['promotion_price']);
                    //合并计算了优惠券金额权重后的商品
                    $coalescing_goods = self::coalescingPromotionGoods($seller_goods['goods'], $promotion_goods);
                    $coalescing_price = self::sumGoodsPrice($coalescing_goods);

                    //组装商品
                    $format_goods[$seller_id]['goods'] = $coalescing_goods;
                    $format_goods[$seller_id]['price'] = $coalescing_price;
                    $format_goods[$seller_id]['coupons_id'] = $coupons_id;
                    $format_goods[$seller_id]['promotion'][] = array('title' => $coupons_data['coupons']['title'], 'price' => $coupons_data['promotion_price']);
                } else {
                    api_error(__('api.coupons_no_use'));
                }
            }
        }
        return $format_goods;
    }

    /**
     * 合并计算了优惠券金额权重后的商品
     * @param $format_goods
     * @param $coupons
     * @return array
     */
    public static function coalescingPromotionGoods(array $format_goods, array $promotion_goods)
    {
        if ($format_goods && $promotion_goods) {
            $new_format_goods = array();
            foreach ($format_goods as $goods) {
                $new_format_goods[$goods['sku_id']] = $goods;
            }
            foreach ($promotion_goods as $goods) {
                $new_format_goods[$goods['sku_id']]['promotion_price'] = $goods['promotion_price'];
            }
            return $new_format_goods;
        }
        return false;
    }

    /**
     * 获取会员折扣价格
     * @param $goods
     * @param $is_special 是否活动商品
     * @return mixed
     */
    public static function getVipPrice(array $goods, $is_special = false)
    {
        if ($is_special) {
            //活动商品不参与会员折扣,价格和库存使用活动对应的
            $pct = '';
            $goods['show_price'] = $goods['activity_price'];
            $goods['line_price'] = $goods['sell_price'];
            $goods['stock'] = $goods['activity_stock'];
        } else {
            $group_data = get_user_group();
            $pct = isset($group_data['pct']) ? $group_data['pct'] : '';
            $goods['show_price'] = $goods['sell_price'];
            $goods['line_price'] = $goods['market_price'];
        }
        if ($pct) {
            $goods['show_price'] = format_price($goods['sell_price'] * $pct);
            $goods['line_price'] = $goods['market_price'];
        }
        unset($goods['sell_price'], $goods['market_price'], $goods['activity_stock'], $goods['activity_price']);
        return $goods;
    }

    /**
     * 格式化参数
     * @param string $params
     */
    public static function formatParams()
    {
        $coupons = request()->input('coupons');
        if ($coupons) $coupons = json_decode($coupons, true);

        $note = request()->input('note');
        if ($note) $note = json_decode($note, true);

        $delivery = request()->input('delivery');
        if ($delivery) $delivery = json_decode($delivery, true);

        $invoice = request()->input('invoice');
        if ($invoice) $invoice = json_decode($invoice, true);

        return [$coupons, $delivery, $note, $invoice];
    }

    /**
     * 验证发票信息
     * @param $seller_goods
     * @param $invoice
     * @return mixed
     * @throws \App\Exceptions\ApiException
     */
    public static function checkInvoice($seller_goods, $invoice)
    {
        foreach ($seller_goods as $value) {
            $seller_id = $value['seller']['id'];
            if (isset($invoice[$seller_id]) && $invoice[$seller_id]) {
                $_seller_invoice = $invoice[$seller_id];
                if ($value['seller']['invoice'] == Seller::INVOICE_ON) {
                    if (!isset($_seller_invoice['title']) || !$_seller_invoice['title']) {
                        api_error(__('api.invoice_title_error'));
                    }
                    if (!isset(OrderInvoice::TYPE_DESC[$_seller_invoice['type']])) {
                        api_error(__('api.invalid_params'));
                    }
                    if ($_seller_invoice['type'] == OrderInvoice::TYPE_ENTERPRISE && !$_seller_invoice['tax_no']) {
                        api_error(__('api.invoice_tax_no_error'));
                    }
                } else {
                    unset($invoice[$seller_id]);
                }
            }
        }
        return $invoice;
    }
}
