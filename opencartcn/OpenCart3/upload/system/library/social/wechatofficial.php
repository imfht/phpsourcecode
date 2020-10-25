<?php
/**
 * wechat.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-04-01 16:16
 * @modified   2019-04-01 16:16
 */

namespace Social;

class WechatOfficial extends Base
{
    const PROVIDER = 'wechatofficial';

    private $clientId;
    private $clientSecret;
    private $redirect;
    private $request;
    private $driver;

    public function __construct($socialData = [])
    {
        parent::__construct(self::PROVIDER);
        if (empty($socialData)) {
            $socialData = $this->getSocialByProvider(self::PROVIDER);
        }
        $this->clientId = array_get($socialData, 'client_id');
        $this->clientSecret = array_get($socialData, 'client_secret');
        $this->redirect = array_get($socialData, 'redirect');
        $this->request = request();
        $options = array(
            'appid' => $this->clientId,
            'appsecret' => $this->clientSecret,
            'cachepath' => DIR_LOGS . 'wechat/'
        );
        \Wechat\Loader::config($options);
        $this->driver = new \Wechat\WechatOauth();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function redirectAuthUrl()
    {
        $redirectUrl = $this->driver->getOauthRedirect($this->redirect, 'guangda_network', 'snsapi_userinfo');
        $this->logInfo("Redirect URL: {$redirectUrl}");
        if ($redirectUrl === false) {
            throw new \Exception('Error: getOauthRedirect!');
        } else {
            return $redirectUrl;
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getUserData()
    {
        $result = $this->driver->getOauthAccessToken();
        if ($result === false) {
            throw new \Exception('Error: getOauthAccessToken!');
        }

        $userInfo = $this->driver->getOauthUserinfo($result['access_token'], $result['openid']);
        $this->logInfo("UserData:", $userInfo);
        if (!isset($userInfo['unionid'])) {
            $userInfo['unionid'] = '';
        }

        $userData = array(
            'provider' => self::PROVIDER,
            'uid' => array_get($userInfo, 'openid'),
            'name' => array_get($userInfo, 'nickname'),
            'union_id' => array_get($userInfo, 'unionid'),
            'access_token' => $result['access_token'],
            'token_secret' => '',
            'avatar' => array_get($userInfo, 'headimgurl')
        );
        return $userData;
    }

    public function getAppId() {
        return $this->clientId;
    }

    public function getAppSecret() {
        return $this->clientSecret;
    }
}
