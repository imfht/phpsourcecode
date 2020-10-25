<?php

/**
 * 微信扫码支付
 */

namespace app\wechat\controller;

class WebPayController extends \app\base\controller\SiteController {

    protected $_middle = 'wechat/QrcodePay';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'token' => request('get', 'token'),
            'data' => request('get')
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->assign('site', $this->siteConfig);
            $this->display();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function status() {
        target($this->_middle, 'middle')->setParams([
            'order_no' => request('', 'order_no'),
        ])->status()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}