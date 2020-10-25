<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/18
 * Time: 下午4:03
 */

namespace App\Services;

use App\Models\Delivery;

class DeliveryService
{
    /**
     * 计算运费
     * @param $seller_id 商家id
     * @param $goods 商品
     * @param $prov_id 省份
     * return array 运费金额，不配送商品
     */
    public static function getPrice($seller_id = 0, $goods = array(), $prov_id = 0)
    {
        $return = array(
            'delivery_price' => 0,//原始运费
            'delivery_price_real' => 0,//实际支付运费
            'sku_ids' => array()//无法配送的商品
        );
        if ($seller_id && $goods && $prov_id) {
            //格式化商品信息按邮费模板分组
            $group = self::formatGroup($goods);
            //计算每组运费模板的价格
            $no_delivery_ids = array();
            foreach ($group as $delivery_id => $sku) {
                $all_buy_qty = $all_weight = $total_price = 0;
                $sku_ids = array();
                foreach ($sku as $value) {
                    $all_buy_qty += $value['buy_qty'];
                    $all_weight += $value['weight'] * $value['buy_qty'];
                    $total_price += $value['show_price'] * $value['buy_qty'];
                    $sku_ids[] = $value['sku_id'];//当前配送模板下的商品id
                }
                $res_delivery = self::getDelivery($all_buy_qty, $all_weight, $total_price, $delivery_id, $prov_id);
                $return['delivery_price'] += $res_delivery['delivery_price'];
                $return['delivery_price_real'] += $res_delivery['delivery_price_real'];
                if ($res_delivery['is_delivery'] == 0) {
                    $return['sku_ids'] = array_merge($return['sku_ids'], $sku_ids);
                }
            }
        }
        $return['delivery_price'] = format_price($return['delivery_price']);
        $return['delivery_price_real'] = format_price($return['delivery_price_real']);
        return $return;
    }

    /**
     * 格式化商品信息按邮费模板分组
     * @param array $goods 参与分组的商品
     * @return array
     */
    public static function formatGroup($goods = array())
    {
        if (!$goods) {
            return array();
        }
        $group = array();
        foreach ($goods as $sku) {
            $group[$sku['delivery_id']][] = $sku;
        }
        return $group;
    }

    /**
     * 根据配送方式id计算运费
     * @param $all_buy_qty 购买件数
     * @param $all_weight 商品总重量
     * @param $total_price 商品总金额
     * @param $delivery_id 配送方式ID
     * @param $prov 省份的ID
     */
    public static function getDelivery($all_buy_qty, $all_weight, $total_price, $delivery_id, $prov)
    {
        $return = array(
            'delivery_price' => 0,//原始运费
            'delivery_price_real' => 0,//实际支付运费
            'is_delivery' => 0,
        );
        if ($all_buy_qty && is_numeric($all_weight) && is_numeric($total_price) && $delivery_id && $prov) {
            //查询配送方式详情
            $res_delivery = Delivery::find($delivery_id);
            if (!$res_delivery) {
                api_error(__('api.delivery_error'));
            } elseif ($res_delivery['status'] != Delivery::STATUS_ON) {
                api_error(__('api.delivery_error'));
            }

            $delivery_price = 0;//运费
            $is_delivery = 1;//是否能送达1为可以送达

            $delivery_data = array(
                'type' => $res_delivery['type'],
                'first_weight' => $res_delivery['first_weight'],
                'second_weight' => $res_delivery['second_weight'],
                'free_type' => $res_delivery['free_type'],
                'free_price' => $res_delivery['free_price'],
            );
            //当配送方式是统一配置的时候，不区分地区
            if ($res_delivery['price_type'] == Delivery::PRICE_TYPE_UNIFIED) {
                $delivery_data['first_price'] = $res_delivery['first_price'];
                $delivery_data['second_price'] = $res_delivery['second_price'];
            } else {
                //当配送方式为指定区域和价格的时候
                $special = false;
                $special_key = '';

                $group_area_id = json_decode($res_delivery['group_area_id'], true);
                if (!$group_area_id) {
                    return $return;
                }
                foreach ($group_area_id as $key => $val) {
                    //匹配到了特殊的省份运费价格
                    if (in_array($prov, $val)) {
                        $special = true;
                        $special_key = $key;
                        break;
                    }
                }
                //匹配到了特殊的省份运费价格
                if ($special) {
                    $group_data = json_decode($res_delivery['group_json'], true);
                    $special_data = $group_data[$special_key];
                    $delivery_data['type'] = $special_data['type'];
                    $delivery_data['first_price'] = $special_data['first_price'];
                    $delivery_data['second_price'] = $special_data['second_price'];
                    $delivery_data['first_weight'] = $special_data['first_weight'];
                    $delivery_data['second_weight'] = $special_data['second_weight'];
                    $delivery_data['free_type'] = $special_data['free_type'];
                    $delivery_data['free_price'] = $special_data['free_price'];
                } else {
                    //判断是否设置默认费用了
                    if ($res_delivery['open_default'] == Delivery::OPEN_DEFAULT_ON) {
                        $delivery_data['first_price'] = $res_delivery['first_price'];
                        $delivery_data['second_price'] = $res_delivery['second_price'];
                    } else {
                        $delivery_price = 0;
                        $is_delivery = 0;
                    }
                }
            }
            //不可配送的直接返回
            if ($is_delivery != 1) {
                return $return;
            } else {
                $delivery_price = $delivery_price_real = self::getDeliveryPrice($all_buy_qty, $all_weight, $delivery_data);//计算运费
                if ($delivery_data['free_type'] == Delivery::FREE_TYPE_MONEY) {
                    if ($total_price >= $delivery_data['free_price'] && $delivery_data['free_price'] > 0) {
                        $delivery_price_real = 0;
                    }
                } elseif ($delivery_data['free_type'] == Delivery::FREE_TYPE_NUMBER) {
                    if ($all_buy_qty >= $delivery_data['free_price'] && $delivery_data['free_price'] > 0) {
                        $delivery_price_real = 0;
                    }
                }
                $return = array(
                    'delivery_price' => $delivery_price,
                    'delivery_price_real' => $delivery_price_real,
                    'is_delivery' => 1,
                );
            }
            return $return;
        }
    }

    /**
     * 根据重量或件数计算给定价格
     * @param $all_buy_qty 总件数
     * @param $all_weight 总重量
     * @param $delivery_data array 配送方式数据
     */
    public static function getDeliveryPrice($all_buy_qty, $all_weight, $delivery_data)
    {
        if ($delivery_data['type'] == Delivery::TYPE_WEIGHT) {
            $value = $all_weight;
        } else if ($delivery_data['type'] == Delivery::TYPE_NUMBER) {
            $value = $all_buy_qty;
        }
        //当商品总重量(或件数)小于或等于首重(件)的时候
        if ($value <= $delivery_data['first_weight']) {
            return $delivery_data['first_price'];
        }
        //当商品重量(或件数)大于首重(件)时，根据次重(件)进行累加计算
        $num = ceil(($value - $delivery_data['first_weight']) / $delivery_data['second_weight']);
        $price = $delivery_data['first_price'] + ($delivery_data['second_price'] * $num);
        return $price;
    }

}