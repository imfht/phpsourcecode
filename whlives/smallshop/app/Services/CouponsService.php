<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/18
 * Time: 下午4:03
 */

namespace App\Services;

use App\Models\Coupons;
use App\Models\CouponsDetail;

class CouponsService
{
    /**
     * 获取优惠券
     * @param $seller_id 商家id
     * @param $goods 商品
     * return array 优惠券
     */
    public static function getCoupons($seller_id, $goods = array())
    {
        $return = array(
            'valid_coupons' => array(),
            'invalid_coupons' => array()
        );
        $where = array(
            ['status', CouponsDetail::STATUS_OFF],
            ['m_id', get_user_id()],
            ['is_use', CouponsDetail::USE_OFF],
            ['start_at', '<=', get_date()],
            ['end_at', '>=', get_date()],
        );
        $my_coupons = CouponsDetail::where($where)->select('id', 'coupons_id', 'code')->get();
        if ($my_coupons->isEmpty()) {
            return $return;
        }
        $coupons_ids = array();
        foreach ($my_coupons as $value) {
            $coupons_ids[] = $value['coupons_id'];
        }
        $res_coupons = Coupons::where(['seller_id' => $seller_id, 'status' => Coupons::STATUS_ON])->whereIn('id', $coupons_ids)->get();
        if ($res_coupons->isEmpty()) {
            return $return;
        }

        $invalid = $valid = array();
        //开始过滤满足条件的优惠券
        foreach ($res_coupons->toArray() as $coupons) {
            $conform_rule = self::getConformRule($coupons, $goods);
            $amount = $coupons['amount'];
            if ($coupons['type'] == Coupons::TYPE_DISCOUNT) {
                $amount = intval($coupons['amount'] / 10);//折扣券
            }
            $_item = array(
                'title' => $coupons['title'],
                'type' => $coupons['type'],
                'amount' => $amount,
                'use_price' => $coupons['use_price'],
                'image' => $coupons['image'],
                'start_at' => $coupons['start_at'],
                'end_at' => $coupons['end_at'],
                'note' => $coupons['note'],
            );
            if ($conform_rule) {
                $valid[$coupons['id']] = $_item;
            } else {
                $invalid[$coupons['id']] = $_item;
            }
        }

        $invalid_coupons = $valid_coupons = array();
        foreach ($my_coupons as $value) {
            $coupons_id = $value['coupons_id'];
            if (isset($valid[$coupons_id])) {
                $_item = $valid[$coupons_id];
                $_item['id'] = $value['id'];
                $valid_coupons[] = $_item;
            } elseif(isset($invalid[$coupons_id])) {
                $_item = $invalid[$coupons_id];
                $_item['id'] = $value['id'];
                $invalid_coupons[] = $_item;
            }
        }
        $return = array(
            'valid_coupons' => $valid_coupons,
            'invalid_coupons' => $invalid_coupons
        );
        return $return;
    }

    /**
     * 验证优惠券是否可用
     * @param $seller_id 商家id
     * @param $coupons_id 优惠券id
     * @param $goods 商品
     * return array 优惠券
     */
    public static function checkCoupons($seller_id, $coupons_id, $goods = array())
    {
        $return = array(
            'valid_coupons' => array(),
            'invalid_coupons' => array()
        );
        $where = array(
            ['id', $coupons_id],
            ['status', CouponsDetail::STATUS_OFF],
            ['m_id', get_user_id()],
            ['start_at', '<=', get_date()],
        );
        $coupons_detail = CouponsDetail::where($where)->first();
        if (!$coupons_detail) {
            api_error(__('api.coupons_not_exists'));
        } elseif ($coupons_detail['is_use'] == CouponsDetail::USE_ON) {
            api_error(__('api.coupons_is_use'));
        } elseif ($coupons_detail['end_at'] <= get_date()) {
            api_error(__('api.coupons_overdue'));
        }
        //查询优惠券详情
        $coupons = Coupons::where(['seller_id' => $seller_id, 'status' => Coupons::STATUS_ON, 'id' => $coupons_detail['coupons_id']])->first();
        if (!$coupons) {
            api_error(__('api.coupons_not_exists'));
        }

        $conform_rule = self::getConformRule($coupons, $goods);
        if ($conform_rule) {
            $conform_rule['coupons']['title'] = $coupons['title'];
            return $conform_rule;
        }
        return false;
    }

    /**
     * 检验优惠券是否满足条件
     * @param $coupons
     * @param $goods
     * @return array|bool 检验通过返回满足的商品sku_id和与会金额
     */
    public static function getConformRule($coupons, $goods)
    {
        if (!$coupons || !$goods) return false;
        $rules = json_decode($coupons['rule'], true);
        $sku_ids = array();
        foreach ($rules as $where => $rule_value) {
            //根据字段冒号后的来区分条件，冒号前是条件字段
            $where_arr = explode(':', $where);
            $is_in = false;
            if ($where_arr[1] == 'in') {
                $is_in = true;
            }
            $sku_ids[$where] = self::getConformSkuid($goods, $where_arr[0], $rule_value, $is_in);
        }
        if (!$sku_ids) return false;//没有满足条件的优惠券
        $conform_sku_id = self::getSkuid($goods);//默认所有商品可用
        foreach ($sku_ids as $sku_id) {
            $conform_sku_id = array_intersect($conform_sku_id, $sku_id);
        }
        $conform_sku_id = array_unique($conform_sku_id);

        //计算满足条件的商品金额
        if ($conform_sku_id) {
            $total_price = 0;
            foreach ($goods as $sku) {
                if (in_array($sku['sku_id'], $conform_sku_id)) {
                    $total_price += $sku['show_price'] * $sku['buy_qty'];
                }
            }
            $total_price = format_price($total_price);
            //判断商品金额是否满足优惠券,并计算符合的优惠券优惠金额
            if ($total_price >= $coupons['use_price']) {
                if ($coupons['type'] == Coupons::TYPE_REDUCTION) {
                    $promotion_price = $coupons['amount'];
                } elseif ($coupons['type'] == Coupons::TYPE_DISCOUNT) {
                    $promotion_price = $total_price - ($total_price * ($coupons['amount'] / 100));
                }
                $return = array(
                    'sku_id' => $conform_sku_id,
                    'promotion_price' => format_price($promotion_price)
                );
                return $return;
            }
        }
        return false;
    }

    /**
     * 获取满足条件商品sku_id
     * @param $goods
     * @param $id_name
     * @param $rule_value
     * @param bool $is_in
     * @return array
     */
    public static function getConformSkuid($goods, $id_name, $rule_value, $is_in = true)
    {
        $sku_ids = array();
        foreach ($goods as $value) {
            if (!$rule_value) {
                $sku_ids[] = $value['sku_id'];//没有填写的时候默认都可以
            } elseif (in_array($value[$id_name], $rule_value) && $is_in) {
                $sku_ids[] = $value['sku_id'];
            } elseif (!in_array($value[$id_name], $rule_value) && !$is_in) {
                $sku_ids[] = $value['sku_id'];
            }
        }
        return $sku_ids;
    }

    /**
     * 获取商品sku_id
     * @param $goods
     * @return array
     */
    public static function getSkuid($goods)
    {
        $sku_ids = array();
        foreach ($goods as $value) {
            $sku_ids[] = $value['sku_id'];
        }
        return $sku_ids;
    }


}
