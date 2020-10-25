<?php

/**
 * 微信接口
 */

namespace app\wechat\service;
use EasyWeChat\Foundation\Application;

class WechatService extends \app\base\service\BaseService {

    public $wechat = null;
    public $config = [];

    /**
     * 实例化微信服务
     * WechatService constructor.
     */
    public function __construct() {
        if(empty($this->config)) {
            $this->init();
        }
    }

    /**
     * 初始化微信类
     * @param array $config
     */
    public function init($config = []) {
        $this->config = target('wechat/WechatConfig')->getConfig();
        $this->config = array_merge($this->config, $config);

        $options = [
            'app_id' => $this->config['appid'],
            'secret' => $this->config['secret'],
            'token' => $this->config['token'],
            'aes_key' => $this->config['aeskey'],
            'log' => [
                'level' => 'debug',
                'permission' => 0775,
                'file'  => DATA_PATH . 'log/wechat_' . date('y-m-d') . '.log',
            ],
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => $this->config['oauth_url'] ? $this->config['oauth_url'] : url(LAYER_NAME . '/wechat/Login/connect'),
            ],
            'guzzle' => [
                'timeout' => 20,
            ],
        ];
        $this->wechat = new Application($options);
        return $this->wechat;
    }

    /**
     * 获取微信对象
     * @return null
     */
    public function wechat() {
        return $this->wechat;
    }

    /**
     * 获取配置文件
     * @return array
     */
    public function config() {
        return $this->config;
    }
}

