<?php

namespace extend\weapp;

use extend\weapp\api\AccessToken;
use extend\weapp\api\CreateQrCode;
use extend\weapp\api\JsCodeToSession;
use extend\weapp\api\MediaGet;
use extend\weapp\api\MediaUpload;
use extend\weapp\api\MessageCustomSend;
use extend\weapp\api\MessageWxOpenTemplateSend;
use traits\Instance;

/**
 * @property AccessToken $accessToken
 * @property CreateQrCode $createQrCode
 * @property JsCodeToSession $jsCodeToSession
 * @property MessageCustomSend $messageCustomSend
 * @property MessageWxOpenTemplateSend $messageWxOpenTemplateSend
 * @property MediaGet $mediaGet
 * @property MediaUpload $mediaUpload
 */
class WeAppProgram
{
    use Instance;
    protected $config;

    public function __construct($config = null)
    {
        if ($config == null || is_array($config)) {
            $this->config = new Config($config);
        }
        if ($config instanceof Config) {
            $this->config = $config;
        }
        if (empty($this->config)) {
            throw new \Exception('no config');
        }
        return $this;
    }

    public function __get($api)
    {
        try {
            $classname = "\\extend\\weapp\\api\\" . ucfirst($api);
            if (!class_exists($classname)) {
                throw new \Exception('api undefined');
                return false;
            }
            $new = new $classname($this->config);
            if ($new::NEED_ACCESS_TOKEN == true) {
                $new->setAccessToken($this->getToken());
            }
            return $new;
        } catch (\Exception $e) {
            throw new \Exception('api undefined');
        }

    }

    protected function getToken()
    {
        $getAccessToken = $this->config->getAccessToken();
        if (empty($getAccessToken)) {
            $token = $this->accessToken->getToken();
            $this->config->setAccessToken($token->accessToken, $token->expiresIn);
        }
        return $this->config->getAccessToken();
    }
}