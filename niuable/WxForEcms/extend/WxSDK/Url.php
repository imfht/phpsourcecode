<?php
namespace WxSDK;

use WxSDK\core\common\IApiUrl;

class Url implements IApiUrl
{
    private $template;
    function __construct(string $template){
        $this->template = $template;
    }
    public function getUrl(String $accessToken){
        return str_replace("ACCESS_TOKEN", $accessToken, $this->template);
    }
}