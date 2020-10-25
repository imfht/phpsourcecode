<?php
/**
 * google.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-05-07 10:17
 * @modified   2019-05-07 10:17
 */


namespace Social;


class Google extends Base
{
    const PROVIDER = 'google';

    private $clientId;
    private $clientSecret;
    private $redirect;
    private $request;
    private $driver;

    /**
     * Google constructor.
     * @param array $socialData
     * @throws \Exception
     */
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

    /**
     * @return string
     */
    public function redirectAuthUrl()
    {
        //$this->driver->scopes(['https://www.googleapis.com/auth/userinfo.email']);
        $redirectUrl = $this->driver->redirect()->getTargetUrl();
        $this->logInfo("Redirect URL: {$redirectUrl}");
        return $redirectUrl;
    }

    /**
     * @return array
     */
    public function getUserData()
    {
        $user = $this->driver->stateless()->user();
        $this->logInfo("UserData:", $user);
        $token = $user->getAccessToken();
        $userData = array(
            'provider' => self::PROVIDER,
            'uid' => $user->getId(),
            'union_id' => '',
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'access_token' => $token->getToken(),
            'token_secret' => '',
            'avatar' => $user->getAvatar()
        );
        return $userData;
    }
}
