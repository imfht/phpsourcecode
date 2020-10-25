<?php
/**
 * facebook.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-04-01 18:30
 * @modified   2019-04-01 18:30
 */

namespace Social;

class Facebook extends Base
{
    const PROVIDER = 'facebook';

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
        $redirectUrl = $this->driver->redirect()->getTargetUrl();
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
}