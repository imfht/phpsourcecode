<?php

/**
 * 微信支付
 */

namespace app\wechat\mobile;

class PayMobile extends  \app\member\mobile\MemberMobile {

    protected $noLogin = true;
    protected $_middle = 'wechat/MobilePay';

    protected $oauth = null;
    public $wechat = null;
    public $config = [];

    public function initPay($url = '') {
        $config = target('wechat/WechatMobile', 'pay')->getConfig('', request('get', 'city', 0, 'intval'));
        $target = target('wechat/Wechat', 'service');
        $target->init([
            'oauth_url' => $url ? $url : url('index'),
            'appid' => $config['app_id'],
            'secret' => $config['secret']
        ]);
        $this->wechat = $target->wechat();
        $this->oauth = $this->wechat->oauth;
    }

    public function auth() {
        $data = $this->getData();
        $this->initPay(url('index') . '?' . http_build_query($data));
        $response = $this->oauth->redirect();
        $response->send();
    }

    private function getData() {
        $getData = request('get');
        return [
            'body' => urldecode($getData['body']),
            'order_no' => $getData['order_no'],
            'money' => $getData['money'],
            'app' => $getData['app'],
            'ip' => $getData['ip'],
            'url' => $getData['url'],
            'token' => $getData['token']
        ];
    }


    public function index() {
        $get = request('get');
        $getData = $this->getData();
        if(empty($get['openid'])) {
            $this->initPay();
            $user = $this->oauth->user();
            $getData['openid'] = $user->getId();
            $this->redirect(url('index') . '?' . http_build_query($getData));
        }
        unset($getData['token']);
        $token = $get['token'];
        if(!data_sign_has($getData, $token)) {
            $this->error('支付数据验证失败!');
        }

        $getData['openid'] = $get['openid'];
        $data = target('wechat/WechatMobile', 'pay')->getParams($getData);
        if(!$data) {
            $this->errorCallback(target('wechat/WechatMobile', 'pay')->getError());
        }
        $data['url'] = urldecode($getData['url']);
        target($this->_middle, 'middle')->meta()->export(function ($pageData) use ($data, $get) {
            $this->assign($pageData);
            $this->assign('getData', $get);
            $this->assign('data', $data);
            $this->memberDisplay('', false);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function browser() {
        $getData = $this->getData();
        $this->initPay();
        $payUrl = url("auth", $getData, true, false, false);
        $urlData = $this->wechat->url->shorten($payUrl);
        $shareUrl = $urlData['short_url'];
        target($this->_middle, 'middle')->meta()->export(function ($data) use ($shareUrl, $getData) {
            $this->assign($data);
            $this->assign('getData', $getData);
            $this->assign('shareUrl', $shareUrl);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}