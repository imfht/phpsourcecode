<?php
namespace WxSDK\core\model\poi;

class Poi
{
    public $base_info;
    function __construct(BaseInfo $param) {
        $this->base_info = $param;
    }
}

