<?php
require_once dirname(__FILE__) . '/wechat.class.php';
class wechat_oauth
{
    private $_need_request = array('code', 'state');

    public function __construct($setting) {
        //组装回调地址
        $this->redirect_uri = U('oauth/callback', array('mod'=>'wechat'), '', '', true);
        $this->setting = $setting;
    }
    /**
     * 获取授权地址
     */
    function getAuthorizeURL() {
        $oauth = new Wechat($this->setting['app_key'], $this->setting['app_secret']);
        return $oauth->getAuthorizeURL($this->redirect_uri);
    }

    /**
     * 获取用户信息
     */
    public function ogetUserInfo($request_args) {
        $oauth = new Wechat($this->setting['app_key'], $this->setting['app_secret']);
        $keys = array('code'=>$request_args['code'], 'state'=>$request_args['state'], 'redirect_uri'=>$this->redirect_uri);
        $token = $oauth->getAccessToken($keys);
        $openid = $token["openid"];
        $user = $oauth->getUserInfo($token["access_token"], $openid);
        //$user['nickname'] = '于斌';
        //$user['headimgurl'] = 'http://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83epC7EgVMXrNCAnfqrApMtj3AEgMFU3ZYP7QTKUm3TMEiakdib5mPMFkCAxSiayeBOlnHssSf0ReRJ95Q/132';
        $result['keyid'] = $openid;
        $result['keyname'] = $user['nickname'];
        //$result['keyavatar_small'] = $user['figureurl'];
        //$result['keyavatar_big'] = $user['figureurl_2'];
        $result['keyavatar_small'] = $user['figureurl'];
        $result['keyavatar_big'] = $user['figureurl'];
        $result['bind_info'] = $token;
        return $result;
    }

    public function NeedRequest() {
        return $this->_need_request;
    }
}