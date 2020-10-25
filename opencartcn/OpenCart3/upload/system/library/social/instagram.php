<?php
/**
 * instagram.php
 *
 * @copyright 2019 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2019-09-2019-09-18 18:05
 * @modified 2019-09-2019-09-18 18:05
 */

namespace Social;

class Instagram extends Base
{
    const PROVIDER = 'instagram';

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
    }

    public function redirectAuthUrl()
    {
        $instagram = new \MetzWeb\Instagram\Instagram(array(
            'apiKey' => $this->clientId,
            'apiSecret' => $this->clientSecret,
            'apiCallback' => $this->redirect
        ));
        $this->instagram = $instagram;
        $redirectUrl = $instagram->getLoginUrl();
        $this->logInfo("Redirect URL: {$redirectUrl}");
        return $redirectUrl;
    }

    public function getUserData()
    {
        $instagram = new \MetzWeb\Instagram\Instagram(array(
            'apiKey' => $this->clientId,
            'apiSecret' => $this->clientSecret,
            'apiCallback' => $this->redirect
        ));
        $code = array_get($this->request->get, 'code', '');
        if (!$code) {
            exit();
        }
        $data = $instagram->getOAuthToken($code);
        $userData = array(
            'provider' => self::PROVIDER,
            'uid' => $data->user->id,
            'union_id' => '',
            'name' => $data->user->username,
            'access_token' => $data->access_token,
            'token_secret' => '',
            'avatar' => $data->user->profile_picture
        );
        return $userData;
    }
}