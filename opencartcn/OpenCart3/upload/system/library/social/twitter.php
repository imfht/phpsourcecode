<?php
/**
 * twitter.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-04-01 16:38
 * @modified   2019-04-01 16:38
 */

namespace Social;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Arr;

class Twitter extends Base
{

    const PROVIDER = 'twitter';

    private $clientId;
    private $clientSecret;
    private $redirect;
    private $request;
    private $driver;
    private $session;

    /**
     * Twitter constructor.
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
        $this->driver = new TwitterOAuth($this->clientId, $this->clientSecret);
        $this->session = registry('session');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function redirectAuthUrl()
    {
        $requestToken = $this->driver->oauth('oauth/request_token', ['oauth_callback' => $this->redirect]);
        if ($this->driver->getLastHttpCode() != 200) {
            throw new \Exception('There was a problem performing this request');
        }
        $this->session->data['twitter_oauth_token'] = $requestToken['oauth_token'];
        $this->session->data['twitter_oauth_token_secret'] = $requestToken['oauth_token_secret'];
        $redirectUrl = $this->driver->url(
            'oauth/authorize', [
                'oauth_token' => $requestToken['oauth_token']
            ]
        );
        $this->logInfo("Redirect URL: {$redirectUrl}");
        return $redirectUrl;
    }

    /**
     * @return array
     * @throws \Abraham\TwitterOAuth\TwitterOAuthException
     */
    public function getUserData()
    {
        $driver = new TwitterOAuth($this->clientId, $this->clientSecret, $this->session->data['twitter_oauth_token'], $this->session->data['twitter_oauth_token_secret']);
        $response = $driver->oauth(
            'oauth/access_token', [
                'oauth_verifier' => $this->request->get['oauth_verifier']
            ]
        );

        $this->logInfo("UserData:", $response);
        $userData = array(
            'provider' => self::PROVIDER,
            'uid' => $response['user_id'],
            'union_id' => '',
            'name' => $response['screen_name'],
            'access_token' => $response['oauth_token'],
            'token_secret' => $response['oauth_token_secret'],
            'avatar' => ''
        );
        return $userData;
    }

    /**
     * @return array
     * @throws \Abraham\TwitterOAuth\TwitterOAuthException
     * @throws \Exception
     */
    public function getAccessToken()
    {
        $oauthToken = registry('session')->data['twitter_oauth_token'];
        $oauthTokenSecret = registry('session')->data['twitter_oauth_token_secret'];
        $this->driver->setOauthToken($oauthToken, $oauthTokenSecret);
        $accessToken = $this->driver->oauth(
            'oauth/access_token', [
                'oauth_verifier' => array_get($this->request->get, 'oauth_verifier')
            ]
        );
        return $accessToken;
    }
}
