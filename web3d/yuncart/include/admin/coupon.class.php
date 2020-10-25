<?php

defined('IN_CART') or die;

/**
 *
 * 优惠券
 * 
 */
class Coupon extends Base
{

    /**
     *
     * 优惠券列表
     * 
     */
    public function index()
    {
        list($page, $pagesize) = $this->getRequestPage();
        $count = DB::getDB()->selectcount("coupon");

        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            $this->data["coupons"] = DB::getDB()->select("coupon", "*", "", "couponid DESC", $this->data['pagearr']['limit'], "couponid");
        }
        $this->data['weburl'] = getConfig("weburl");
        $this->output("coupon_index");
    }

    /**
     *
     * 添加优惠券
     * 
     */
    public function couponadd()
    {
        $this->data["opertype"] = "add";
        $this->data['leftcur'] = "coupon_index";
        $this->output("coupon_oper");
    }

    /**
     *
     * 修改优惠券
     * 
     */
    public function couponedit()
    {
        $couponid = intval($_GET["couponid"]);
        $this->data["opertype"] = "edit";
        $this->data['coupon'] = DB::getDB()->selectrow("coupon", "*", "couponid='$couponid'");
        $this->data["couponid"] = $couponid;
        $this->output("coupon_oper");
    }

    /**
     *
     * 保存优惠券
     * 
     */
    public function couponsave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("coupon");
        switch ($opertype) {
            case 'add':
            case 'edit':
                $couponid = trim($_POST["couponid"]);

                //接受参数
                $deno = intval($_POST["deno"]);
                $subject = trim($_POST["subject"]);
                $endtime = trim($_POST["endtime"]);
                $total = intval($_POST["total"]);
                $restrict = intval($_POST["restrict"]);
                $require = intval($_POST["require"]);


                $data = array("deno" => $deno,
                    "total" => $total,
                    "endtime" => strtotime($endtime),
                    "restrict" => $restrict,
                    "require" => $require,
                    "subject" => $subject);

                if ($couponid) {
                    $ret = DB::getDB()->update("coupon", $data, "couponid='$couponid'");
                    $this->adminlog("al_coupon", array("do" => "edit", "subject" => $subject));
                    $this->setHint(__('edit_success', $text));
                } else {
                    $couponid = DB::getDB()->insert("coupon", $data);
                    $this->adminlog("al_coupon", array("do" => "add", "subject" => $subject));
                    $this->setHint(__('add_success', $text));
                }
                break;
            case 'editfield':
                $field = trim($_REQUEST['field']);
                $ret = false;
                if ($field == "delete") {
                    $couponidstr = $_POST["idstr"];
                    if ($couponidstr) {
                        $couponids = explode(",", $couponidstr);
                        $where = "couponid in " . cimplode($couponids);
                        $coupons = DB::getDB()->selectkv("coupon", "couponid", "subject", $where);
                        foreach ($coupons as $subject) {
                            $this->adminlog("al_coupon", array("do" => "del", "subject" => $subject));
                        }

                        DB::getDB()->delete("coupon", $where);
                        $ret = true;
                    }
                } else if ($field == "publish") { //设置发布属性
                    $couponid = intval($_GET["couponid"]);
                    $this->adminlog("al_coupon", array("do" => "edit", "couponid" => $couponid));
                    $ret = DB::getDB()->updatebool("coupon", "ispublish", "couponid='$couponid'");
                    $this->setHint(__('set_success', array($text, __('publish_property'))));
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

    /**
     *
     * 优惠券活动 详细信息
     * 
     */
    public function userdraw()
    {
        $couponid = empty($_POST["couponid"]) ? 0 : intval($_POST['couponid']);
        $isused = empty($_POST["isused"]) ? '' : trim($_POST["isused"]);
        list($page, $pagesize) = $this->getRequestPage();
        $this->data['couponopt'] = $this->getCoupons($couponid, 'option');
        $whereb = array();

        if ($couponid)
            $whereb['couponid'] = $couponid;

        //是否使用
        if ($isused == 'y') {
            $whereb['isused'] = 1;
        } elseif ($isused == 'n') {
            $whereb['isused'] = 0;
        }

        $onarr = array("on" => "couponid");
        $count = DB::getDB()->joincount("coupon", "user_coupon", $onarr, array("b" => $whereb));

        if ($count) {
            $this->data['pagearr'] = getPageArr($page, $pagesize, $count);
            $this->data["infos"] = DB::getDB()->join("coupon", "user_coupon", $onarr, array("a" => "deno,endtime", "b" => "*"), array("b" => $whereb), array("b" => "addtime DESC"), $this->data['pagearr']['limit']);
        }
        $this->data['isused'] = $isused;
        $this->output("coupon_userdraw");
    }

    /**
     *
     * 获取优惠券活动
     * 
     */
    private function getCoupons($couponid = 0, $returntype = 'array')
    {
        static $coupons;
        $data = array();
        !$coupons && $coupons = DB::getDB()->select("coupon", "*", null, "couponid DESC");
        if ($returntype == "array") {
            return $coupons;
        } else if ($returntype == "option") {
            $arr = array();
            foreach ($coupons as $coupon) {
                $arr[$coupon['couponid']] = "面额:" . $coupon['deno'] . "元，结束时间：" . date("Y-m-d", $coupon['endtime']);
            }
            return array2select($arr, "key", "val", $couponid);
        }
    }

}
