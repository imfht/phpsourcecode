<?php

/**
 *  
 * 促销
 *
 *
 * */
defined('IN_CART') or die;

class Promotion
{

    public static $time;

    /**
     *
     * 获取限时打折
     *
     */
    public static function getDiscount()
    {
        $time = time();
        $where = "ispublish=1 AND begintime < '$time' AND endtime > '$time'";
        $discount = DB::getDB()->selectrow("discount", "*", $where, "discountid DESC");
        return $discount;
    }

    /**
     *
     * 判断某一个商品是否限时打折
     *
     */
    public static function itemInDiscount($itemid)
    {
        $discount = self::getDiscount();
        if (!$discount)
            return false;

        //判断该discount中是否含有该itemid
        $where = "itemid='$itemid' AND discountid='" . $discount['discountid'] . "'";
        $indis = DB::getDB()->selectrow("discount_item", "discount", $where);
        return $indis ? array_merge($discount, $indis) : false;
    }

    /**
     *
     * 判断某一个商品是否团购
     *
     */
    public static function itemInTuan($itemid)
    {
        $time = time();
        $where = "itemid='$itemid' AND ispublish=1 AND begintime<'$time' AND endtime>'$time'";
        $tuan = DB::getDB()->selectrow("tuan", "*", $where);
        return $tuan;
    }

    /**
     *
     * 满就送
     *
     */
    public static function getMan()
    {
        $time = time();
        $where = "ispublish=1 AND begintime < '$time' AND endtime > '$time'";
        $man = DB::getDB()->selectrow("man", "*", $where, "manid DESC");
        return $man;
    }

    /**
     *
     * 满减活动字符串
     *
     */
    public static function getTradeManStr()
    {
        if (empty($_SESSION['cart']['rule']))
            return '';
        $ret = array();
        $rule = $_SESSION['cart']['rule'];

        return $ret ? implode("，", $ret) : "";
    }

    /**
     *
     * 获取优惠信息规则
     *
     */
    public static function getManRule($totalfee = '', $includestr = false)
    {
        $man = self::getMan();
        if (!$man)
            return false;
        $rules = DB::getDB()->select("man_rule", "*", "manid='" . $man['manid'] . "'", "manorder DESC");
        $therule = $strarr = array();
        foreach ($rules as $rule) {
            //满就送需满足的订单价格
            $manorder = getPrice($rule['manorder'], -2, 'float');
            if ($manorder <= $totalfee) {
                $therule = array("subject" => $man['subject'], "manid" => $rule['manid']);
                $minus = getPrice($rule['minus'], -2, 'float'); //减去金额
                if ($minus) {
                    $therule['minus'] = $minus;
                    $includestr && $strarr[] = __("man_minus", getPrice($rule['minus']));
                    $totalfee -= $minus;
                    if ($totalfee >= $manorder) {//如果减去金额后，任然符合条件
                        if ($rule['nofreight']) {
                            $therule['nofreight'] = true;
                            $includestr && $strarr[] = __("no_freight");
                        } elseif ($rule['giftname']) {
                            $therule['gift'] = array('giftname' => $rule['giftname'], 'gifturl' => $rule['gifturl']);
                            $includestr && $strarr[] = __("man_gift", $rule['gift']['gifturl'], $rule['gift']['giftname']);
                        }
                    }
                } elseif ($rule['nofreight']) {//如果只包邮
                    $therule['nofreight'] = true;
                    $includestr && $strarr[] = __("no_freight");
                } elseif ($rule['giftname']) {
                    $therule['gift'] = array('giftname' => $rule['giftname'], 'gifturl' => $rule['gifturl']);
                    $includestr && $strarr[] = __("man_gift", $rule['gift']['gifturl'], $rule['gift']['giftname']);
                }
                if ($includestr)
                    $therule['str'] = implode(',', $strarr);
                return $therule;
            }
        }
        return false;
    }

    /**
     *
     * 商品的赠品
     *
     */
    public static function getGifts($itemid)
    {
        $gift = DB::getDB()->selectrow("gifts", "*", "itemid='$itemid' AND ispublish=1");
        return $gift && !empty($gift['gift']) ? unserialize($gift['gift']) : array();
    }

    /**
     *
     * 获取搭配套餐
     *
     */
    public static function getMeal($id, $type = 'itemid')
    {
        $time = time();
        $where = "ispublish=1 AND begintime<'$time' AND endtime>'$time'";
        if ($type == 'itemid') {
            $where .= " AND itemid='$id'";
        } elseif ($type == 'mealid') {
            $where .= " AND mealid='$id'";
        }

        $meal = DB::getDB()->selectrow("meal", "*", $where, "mealid DESC");
        if ($meal) {
            $meal['items'] = DB::getDB()->select("meal_item", "*", "mealid='" . $meal['mealid'] . "'", "order", "", "itemid");
        }
        return $meal;
    }

    /**
     *
     * 获取用户所有优惠券
     *
     */
    public static function getCoupons($uid, $type = 'canuse', $limit = '')
    {
        $curtime = time();
        $where = "a.uid='$uid' AND b.ispublish=1"; //当前用户的，已经发布
        if ($type == 'canuse') {
            $where .= " AND a.isused=0 AND b.endtime>'$curtime'"; //未使用，未过期
        }
        $coupons = DB::getDB()->join("user_coupon", "coupon", array("on" => "couponid"), array("b" => "couponid,endtime,deno,restrict,require", "a" => "addtime,isused,tradeid"), $where, array("a" => "endtime ASC"), $limit);
        return $coupons;
    }

    /**
     *
     * 获取用户的优惠券数量
     *
     */
    public static function getCouponsCount($uid, $type = 'canuse')
    {
        $curtime = time();
        $where = "a.uid='$uid' AND b.ispublish=1"; //当前用户的，已经发布
        if ($type == 'canuse') {
            $where .= " AND a.isused=0 AND b.endtime>'$curtime'"; //未使用，未过期
        }
        $count = DB::getDB()->joincount("user_coupon", "coupon", array("on" => "couponid"), $where);
        return $count;
    }

    /**
     *
     * 判断用户是否有某优惠券，且是否可用
     *
     */
    public static function getCoupon($uid, $couponid, $type = 'canuse')
    {
        $curtime = time();
        $where = "a.couponid='$couponid' AND a.uid='$uid' AND b.ispublish=1"; //当前用户的，已经发布
        if ($type == 'canuse') {
            $where .= " AND a.isused=0 AND b.endtime>'$curtime'"; //未使用，未过期
        }

        $coupon = DB::getDB()->joinrow("user_coupon", "coupon", array("on" => "couponid"), array("b" => "couponid,endtime,deno,restrict,require", "a" => "addtime"), $where);
        return $coupon;
    }

}
