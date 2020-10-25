<?php
/**
 * paypal.php
 *
 * @copyright 2020 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2020-06-2020/6/30 10:42
 * @modified 2020-06-2020/6/30 10:42
 */

namespace Social;

class paypal extends Base
{
    const PROVIDER = 'paypal';

    private $clientId;
    private $clientSecret;
    private $redirect;
    private $request;
    private $sandboxUrl = 'https://www.sandbox.paypal.com/connect';
    private $liveUrl = 'https://www.paypal.com/connect';
    private $sandboxApiUrl = 'https://api.sandbox.paypal.com';
    private $liveApiUrl = 'https://api.paypal.com';

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
        $url = config('module_omni_auth_debug') ? $this->sandboxUrl : $this->liveUrl;
        $scope = implode(' ', ['openid', 'profile', 'email', 'address']);
        $redirect = urlencode($this->redirect);
        $params = [
            "flowEntry=static",
            "client_id={$this->clientId}",
            "response_type=code",
            "scope={$scope}",
            "redirect_uri={$redirect}",
        ];
        return $url . '/?' . implode('&', $params);
    }

    public function getUserData()
    {
        $code = array_get($this->request->get, 'code', '');
        if (!$code) {
            echo 'PayPal code missed!';
            exit();
        }
        $apiUrl = config('module_omni_auth_debug') ? $this->sandboxApiUrl : $this->liveApiUrl;
        $apiTokenUrl = $apiUrl . '/v1/identity/openidconnect/tokenservice';

        $request  = '';
        $request .= 'grant_type=authorization_code';
        $request .= '&code=' . $code;

        $additionalOpts = array(
            CURLOPT_USERPWD    => $this->clientId . ':' . $this->clientSecret,
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => $request
        );

        $accessTokenResponseObject = $this->curl($apiTokenUrl, $additionalOpts);
        if (!isset($accessTokenResponseObject->access_token) || isset($accessTokenResponseObject->error)) {
            echo $accessTokenResponseObject->error;
            die();
        }

        $accessToken = $accessTokenResponseObject->access_token;

        $apiUserUrl = $apiUrl . '/v1/identity/openidconnect/userinfo/?schema=openid';
        $header   = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken;

        $userInfoAdditionalOpts = array(
            CURLOPT_HTTPHEADER => $header,
        );
        $userInfoResponseObject = $this->curl($apiUserUrl, $userInfoAdditionalOpts);
        if (empty($userInfoResponseObject)) {
            echo self::PROVIDER . ' error: get user info failed.';
        }
        return [
            'provider' => self::PROVIDER,
            'uid' => $userInfoResponseObject->user_id,
            'union_id' => '',
            'name' => $userInfoResponseObject->name,
            'access_token' => $accessToken,
            'token_secret' => '',
            'avatar' => ''
        ];
    }

    private function curl($endpoint, $additional_opts = array()) {
        $default_opts = array(
            CURLOPT_PORT           => 443,
            CURLOPT_HEADER         => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE   => 1,
            CURLOPT_FRESH_CONNECT  => 1,
            CURLOPT_URL            => $endpoint,
        );
        $ch = curl_init($endpoint);
        $opts = $default_opts + $additional_opts;
        curl_setopt_array($ch, $opts);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        return $response;
    }
}