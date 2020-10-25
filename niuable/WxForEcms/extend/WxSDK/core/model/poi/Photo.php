<?php
namespace WxSDK\core\model\poi;

class Photo
{
    public $photo_url;
    function __construct(string $photoUrl){
        $this->photo_url = $photoUrl;
    }
}

