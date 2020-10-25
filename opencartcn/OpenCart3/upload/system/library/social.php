<?php
/**
 * social.php
 *
 * @copyright 2020 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2020-06-2020/6/29 13:52
 * @modified 2020-06-2020/6/29 13:52
 */

class Social
{
    private $providerCode;
    private $provider;
    private static $socialProviders = ['facebook', 'twitter', 'instagram', 'google', 'paypal'];
    private static $chSocialProviders = ['wechatofficial', 'qq', 'weibo', 'wechat'];

    public function __construct($providerCode)
    {
        $this->providerCode = $providerCode;
        $providerClass = "\\Social\\" . $providerCode;
        $this->provider = new $providerClass();
    }

    public static function getInstance($providerCode)
    {
        return new self($providerCode);
    }

    public static function getProviderList()
    {
        $socialPath = DIR_SYSTEM . 'library/social';
        $classPaths = glob($socialPath . '/*');
        $classes = [];
        foreach ($classPaths as $classPath) {
            $start = strripos($classPath, '/') + 1;
            $className = strtolower(mb_substr($classPath, $start));
            $className = mb_substr($className, 0, stripos($className, '.'));
            if ($className == 'base') {
                continue;
            }
            $classes[] = $className;
        }
        return $classes;
    }

    public static function getSocialProviders()
    {
        return self::$socialProviders;
    }

    public static function getCnSocialProviders()
    {
        return self::$chSocialProviders;
    }

    public static function getSocialData()
    {
        $socials = config('module_omni_auth_items');

        if (empty($socials)) {
            return [];
        }

        $socialData = [];
        foreach ($socials as $key => $social) {
            $provider = strtolower($social['provider']);
            if (!$social['enabled']) {
                continue;
            } elseif ($provider == 'wechatofficial') {
                continue;
            } elseif (!is_desktop() && $provider == 'wechat') {
                continue;
            }
            $socialData[$key] = $social;
            $socialData[$key]['provider'] = $provider;
            $socialData[$key]['label'] = t("text_{$provider}");
            $socialData[$key]['associated'] = registry('customer')->associated($provider);
        }
        return $socialData;
    }


    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function redirectAuthUrl()
    {
        if (method_exists($this->provider, 'redirectAuthUrl')) {
            try {
                $redirectUrl = $this->provider->redirectAuthUrl();
            } catch (\Exception $e) {
                echo $e->getMessage();
                die();
            }
        }
        if (empty($redirectUrl)) {
            throw new \Exception("Empty redirect url");
        }
        return $redirectUrl;
    }

    public function getAccessToken()
    {
        $accessToken = $this->provider->getAccessToken();
        return $accessToken;
    }

    public function getUserData()
    {
        $userData = $this->provider->getUserData();
        return $userData;
    }
}