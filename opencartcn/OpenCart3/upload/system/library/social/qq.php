<?php
/**
 * qq.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-04-01 16:33
 * @modified   2019-04-01 16:33
 */

namespace Social;

class QQ extends Base
{
    const PROVIDER = 'qq';

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
        $redirectUrl = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id={$this->clientId}&redirect_uri={$this->redirect}&scope=get_user_info";
        $this->logInfo("Redirect URL: {$redirectUrl}");
        return $redirectUrl;
    }

    public function getUserData()
    {
        $user = $this->driver->stateless()->user();
        $this->logInfo("UserData:", $user);
        $token = $user->getAccessToken();
        $userData = array(
            'provider' => self::PROVIDER,
            'uid' => $user->getId(),
            'union_id' => '',
            'name' => $this->getName($user),
            'access_token' => $token->getToken(),
            'token_secret' => '',
            'avatar' => $user->getAvatar()
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
