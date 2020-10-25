<?php

defined('IN_CART') or die;

/**
 *  
 * 优惠券
 *
 *
 * */
class Coupon extends Base
{

    /**
     *
     * 构造函数，赋值uid
     *
     */
    public function __construct($model, $action)
    {
        parent::__construct($model, $action);
        if (!isset($_SESSION["uid"])) {
            cerror(__('need_login'));
        }
        $this->uid = intval($_SESSION["uid"]);
    }

    /**
     *  
     * 领取优惠券
     *
     *
     * */
    public function draw()
    {
        $couponid = intval($_GET["couponid"]);

        //coupon信息
        $coupon = DB::getDB()->selectrow("coupon", "*", "couponid='$couponid'");
        if ($coupon) {
            $time = time();
            //优惠券发布，且当前在有效期内,且尚未领取完
            if ($coupon['ispublish'] && $coupon['endtime'] > $time && $coupon['num'] < $coupon['total']) {
                //限制
                $restrict = intval($coupon["restrict"]);
                if ($restrict) {//判断当前用户领取的量是否已经达到限制
                    $usercount = DB::getDB()->selectcount("user_coupon", "couponid='$couponid' AND uid='" . $this->uid . "'");
                    if ($usercount >= $restrict) {
                        cerror(__("coupon_has_drawed"));
                    }
                }

                //入库
                $data = array("uid" => $this->uid,
                    "uname" => $_SESSION['uname'],
                    "addtime" => $time,
                    "endtime" => $coupon['endtime'],
                    "couponid" => $couponid);
                DB::getDB()->insert("user_coupon", $data);

                //更新
                DB::getDB()->updatecre("coupon", "num", "couponid='$couponid'");
                cerror(__("coupon_draw_success"));
            } else {
                cerror(__("coupon_cannt_draw"));
            }
        }
        cerror(__("coupon_not_exist"));
    }

    /**
     *  
     * 我的优惠券
     *
     *
     * */
    public function mycoupon()
    {
        $this->data['curtime'] = time();
        list($page, $pagesize) = $this->getRequestPage();
        $count = Promotion::getCouponsCount($this->uid, 'all');
        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count, url('index', 'coupon', 'mycoupon'));

            $this->data['coupons'] = Promotion::getCoupons($this->uid, 'all', $this->data["pagearr"]['limit']);
        }
        $this->output("mycoupon");
    }

}
