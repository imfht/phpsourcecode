<?php
namespace app\app\controller;

class WxpayController extends BaseController
{
    //微信支付
    public function index()
    {
        $redirect = urldecode(input('param.redirect'));//支付完成跳转链接
        $jsApiParameters = input('param.jsApiParameters');//支付参数
        
        
        $this->assign('url', $redirect);
        $this->assign('jsApiParameters', $jsApiParameters);
        return view();
    }
}
