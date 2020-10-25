<?php

defined('IN_CART') or die;

/**
 *
 * 支付
 * 
 */
class Payment extends Base
{

    /**
     *
     * 支付
     * 
     */
    public function index()
    {
        $this->data['payments'] = DB::getDB()->select("payment", "*", "", "order");
        $this->output("payment_index");
    }

    /**
     *  
     * 修改支付
     *
     * */
    public function paymentedit()
    {
        $paymentid = intval($_GET["paymentid"]);
        $this->data["payment"] = DB::getDB()->selectrow("payment", "*", "paymentid='$paymentid'");
        $this->data["opertype"] = "edit";
        $this->data["paymentid"] = $paymentid;

        $this->output("payment_oper");
    }

    /**
     *  
     * 保存支付
     *
     * */
    public function paymentsave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __('payment');
        switch ($opertype) {
            case 'edit':
                $name = trim($_POST["name"]);
                $code = trim($_POST["code"]);
                $paymentid = intval($_POST["paymentid"]);
                $ispublish = isset($_POST["ispublish"]) ? 1 : 0;
                if (!$ispublish) {//如果设置为无效,判断是否存在有效的
                    $count = DB::getDB()->selectcount("payment", "ispublish=1 AND paymentid!='$paymentid'");
                    if (!$count) {
                        $this->setHint(__("least_one_payment"));
                    }
                }
                $data = array("name" => $name, "ispublish" => $ispublish);
                if ($code == "alipay") {
                    $paykey = trim($_POST["paykey"]);
                    $paysecret = trim($_POST["paysecret"]);
                    $account = trim($_POST["account"]);
                    $data = array_merge($data, array("paykey" => $paykey, "paysecret" => $paysecret, "account" => $account));
                } else if ($code == "tenpay" || $code == "tenpay2") {
                    $paysecret = trim($_POST["paysecret"]);
                    $account = trim($_POST["account"]);
                    $data = array_merge($data, array("paysecret" => $paysecret, "account" => $account));
                }
                $this->adminlog("al_payment", array("do" => "edit", "name" => $name));
                DB::getDB()->update("payment", $data, "paymentid='$paymentid'");
                $this->setHint(__("edit_success", $text));

            case 'editfield':
                $field = strtolower($_REQUEST["field"]);
                $ret = false;
                if ($field == "publish") {//修改发布状态
                    $paymentid = intval($_GET["paymentid"]);
                    //判断ispublish属性
                    $ispublish = DB::getDB()->selectval("payment", "ispublish", "paymentid='$paymentid'");
                    if ($ispublish) {//必须有一个有效的支付方式
                        $count = DB::getDB()->selectcount("payment", "ispublish=1 AND paymentid!='$paymentid'");
                        if (!$count) {
                            $this->setHint(__("least_one_payment"));
                        }
                    }
                    $this->adminlog("al_payment", array("do" => "edit", "paymentid" => $paymentid));
                    $ret = DB::getDB()->updatebool("payment", "ispublish", "paymentid='$paymentid'");
                    $this->setHint(__('set_success', array($text, __('publish_property'))), "payment_index");
                }
                break;
            case 'save':
                $paymentids = $_POST["paymentid"];
                foreach ($paymentids as $key => $paymentid) {
                    DB::getDB()->update("payment", array("order" => $key + 1), "paymentid='$paymentid'");
                }
                $this->adminlog("al_payment_order");
                $this->setHint(__("edit_success", $text));
                break;
        }
    }

}
