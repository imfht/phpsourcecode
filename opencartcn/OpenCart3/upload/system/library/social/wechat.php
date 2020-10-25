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

class Wechat extends Base
{
    const PROVIDER = 'wechat';

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
        $this->driver = $this->getSocialiteDriver(self::PROVIDER);
    }

    public function redirectAuthUrl()
    {
        $redirectUrl = "https://open.weixin.qq.com/connect/qrconnect?appid={$this->clientId}&redirect_uri={$this->redirect}&response_type=code&scope=snsapi_login&state=guangda_network#wechat_redirect";
        $this->logInfo("Redirect URL: {$redirectUrl}");
        return $redirectUrl;
    }

    public function getUserData()
    {
        $user = $this->driver->user();
        $this->logInfo("UserData:", $user);
        $originalData = $user->getOriginal();
        $this->logInfo("OriginUserData:", $originalData);
        $token = $user->getAccessToken();
        $userData = array(
            'provider' => self::PROVIDER,
            'uid' => $user->getId(),
            'name' => $this->getName($user),
            'union_id' => array_get($originalData, 'unionid'),
            'access_token' => $token->getToken(),
            'token_secret' => '',
            'avatar' => array_get($originalData, 'headimgurl')
        );
        return $userData;
    }

    public function getAccessToken()
    {
        $code = array_get($this->request->get, 'code');
        if (empty($code)) {
            $error = array_get($this->request->get, 'error');
            $errorDescription = array_get($this->request->get, 'error_description');
            if ($error || $errorDescription) {
                throw new \Exception("{$error}:{$errorDescription}");
            }
        }
        $token = $this->driver->getAccessToken($code);
        return $token;
    }
}