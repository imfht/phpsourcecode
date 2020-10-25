<?php
namespace WxSDK\core\model\tpl;

class Miniprogram
{
    public $appid;
    public $pagepath;
    function __construct(string $appid, string $pagePath = NULL) {
        $this->appid = $appid;
        $this->pagepath = $pagePath;
    }
}

