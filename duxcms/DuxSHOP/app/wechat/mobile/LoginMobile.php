<?php

/**
 * 微信登录
 */

namespace app\wechat\mobile;

class LoginMobile extends \app\base\mobile\SiteMobile {

    protected $oauth = null;
    public $wechat = null;
    public $config = [];

    public function __construct() {
        parent::__construct();
        $target = target('wechat/Wechat', 'service');
        $target->init();
        $this->wechat = $target->wechat();
        $this->config = $target->config();
        $this->oauth = $this->wechat->oauth;
    }

    /**
     * 登录跳转
     */
    public function index() {
        $response = $this->oauth->redirect();
        $response->send();
    }

    /**
     * 回调授权
     */
    public function connect() {
        $user = $this->oauth->user();
        $original = $user->getOriginal();

        if($original['unionid']) {
            $info = target('member/MemberConnect')->getWhereInfo([
                'open_id' => $user->getId()
            ]);
            if($info) {
                target('member/MemberConnect')->edit([
                    'connect_id' => $info['connect_id'],
                    'open_id' => $original['unionid'],
                    'pay_id' => $user->getId()
                ]);
            }
            $info = target('member/MemberConnect')->getWhereInfo([
                'open_id' => $original['unionid']
            ]);
            if($info && empty($info['pay_id'])) {
                target('member/MemberConnect')->edit([
                    'connect_id' => $info['connect_id'],
                    'open_id' => $original['unionid'],
                    'pay_id' => $user->getId()
                ]);
            }
        }

        $data = target('member/Member', 'service')->oauthUser('wechat', $original['unionid'] ? $original['unionid'] : $user->getId(), $user->getId(), $user->getName(), $user->getAvatar());
        if(!$data) {
            $this->error(target('member/Member', 'service')->getError(), url('index/Index/index'));
        }

        if($data['status'] == 'bind') {
            $url = url('member/Login/bind', $data['data']);
        }
        if($data['status'] == 'login') {
            \dux\Dux::cookie()->set('user_login', [
                'uid' => $data['data']['uid'],
                'token' => $data['data']['token']
            ], 2592000);
            $url = url('member/Index/index');
        }
        $this->redirect($url);

    }

}
