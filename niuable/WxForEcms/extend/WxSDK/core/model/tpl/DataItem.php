<?php
namespace WxSDK\core\model\tpl;

class DataItem
{
    public $value;
    public $color;
    public $key;
    function __construct(string $key, string $value,string $color = NULL) {
        $this->color = $color;
        $this->value = $value;
        $this->key = $key;
    }
}

