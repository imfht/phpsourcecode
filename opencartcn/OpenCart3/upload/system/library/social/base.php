<?php
/**
 * base.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-04-02 09:24
 * @modified   2019-04-02 09:24
 */

namespace Social;

use Overtrue\Socialite\User;

class Base
{
    protected $socialite;
    private $logger;

    public function __construct($provider)
    {
        $this->socialite = $this->initSocialite();
        $this->logger = new \Log("social/{$provider}");
    }

    /**
     * @param $providerCode
     * @return mixed
     */
    protected function getSocialByProvider($providerCode)
    {
        return array_get($this->getAllSocialData(), $providerCode, []);
    }

    /**
     * @param $providerCode
     * @return \Overtrue\Socialite\Providers\AbstractProvider | \Overtrue\Socialite\ProviderInterface
     */
    protected function getSocialiteDriver($providerCode)
    {
        return $this->socialite->driver($providerCode);
    }

    /**
     * @param $user User
     * @return string|string[]|null
     */
    protected function getName($user)
    {
        $userName = $user->getName();
        if (empty($userName)) {
            $userName = $user->getNickname();
        }
        if (empty($userName)) {
            $userName = $user->getUsername();
        }
        $userName = (new \Kozz\Components\Emoji\EmojiParser())->replace($userName, '');
        return $userName;
    }

    /**
     * @param mixed ...$messages
     */
    protected function logInfo(...$messages)
    {
        if (!config('module_omni_auth_debug')) {
            return;
        }
        foreach ($messages as $message) {
            $this->logger->write($message);
        }
    }


    private function initSocialite()
    {
        $socialData = $this->getAllSocialData();
        $socialite = new \Overtrue\Socialite\SocialiteManager($socialData);
        return $socialite;
    }

    private function getAllSocialData()
    {
        $socialsData = config('module_omni_auth_items');
        $allSocialData = [];
        if (empty($socialsData)) {
            return $allSocialData;
        }
        foreach ($socialsData as $item) {
            $provider = $item['provider'];
            $allSocialData[$provider] = array(
                'client_id' => $item['key'],
                'client_secret' => $item['secret'],
                'redirect' => $item['callback']
            );
        }
        return $allSocialData;
    }
}