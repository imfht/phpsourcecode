<?php
namespace WxSDK\core\model\kefu;

class MenuItem
{
    public $id;
    public $content;
    function __construct(string $id, string $content) {
        $this->content = $content;
        $this->id = $id;
    }
}

