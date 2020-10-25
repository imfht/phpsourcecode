<?php

namespace Home\Controller;

use Think\Controller;

class PublicController extends Controller {

    /**
     * 支付结果返回
     */
    public function notify() {
        $apitype = I('get.apitype');

        $pay = new \Think\Pay($apitype, C('payment.' . $apitype));
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            exit('Access Denied');
        }
        //验证
        if ($pay->verifyNotify($notify)) {
            //获取订单信息
            $info = $pay->getInfo();

            if ($info['status']) {
                $payinfo = M("Pay")->field(true)->where(array('out_trade_no' => $info['out_trade_no']))->find();
                if ($payinfo['status'] == 0 && $payinfo['callback']) {
                    session("pay_verify", true);
                    $check = R($payinfo['callback'], array('money' => $payinfo['money'], 'param' => unserialize($payinfo['param'])));
                    if ($check !== false) {
                        M("Pay")->where(array('out_trade_no' => $info['out_trade_no']))->setField(array('update_time' => time(), 'status' => 1));
                    }
                }
                if (I('get.method') == "return") {
                    redirect($payinfo['url']);
                } else {
                    $pay->notifySuccess();
                }
            } else {
                $this->error("支付失败！");
            }
        } else {
            E("Access Denied");
        }
    }

}
