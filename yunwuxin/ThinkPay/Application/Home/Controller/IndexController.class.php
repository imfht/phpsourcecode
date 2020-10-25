<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {

    public function index() {
        if (IS_POST) {
            //页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
            $paytype = I('post.paytype');

            $pay = new \Think\Pay($paytype, C('payment.' . $paytype));
            $order_no = $pay->createOrderNo();
            $vo = new \Think\Pay\PayVo();
            $vo->setBody("商品描述")
                    ->setFee(I('post.money')) //支付金额
                    ->setOrderNo($order_no)
                    ->setTitle("商品标题")
                    ->setCallback("Home/Index/pay")
                    ->setUrl(U("Home/User/order"))
                    ->setParam(array('order_id' => "goods1业务订单号"));
            echo $pay->buildRequestForm($vo);
        } else {
            //在此之前goods1的业务订单已经生成，状态为等待支付
            $this->display();
        }
    }

    /**
     * 订单支付成功
     * @param type $money
     * @param type $param
     */
    public function pay($money, $param) {
        if (session("pay_verify") == true) {
            session("pay_verify", null);
            //处理goods1业务订单、改名good1业务订单状态
            M("Goods1Order")->where(array('order_id' => $param['order_id']))->setInc('haspay', $money);
        } else {
            E("Access Denied");
        }
    }

}
