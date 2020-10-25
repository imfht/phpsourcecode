<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ie_version_hook
{
    public function check()
    {
        $ie=strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE');
        if($ie){
            $ie_version=substr($_SERVER["HTTP_USER_AGENT"], $ie+5,1);
            if($ie_version < 9){
                header("Location: /incompatible.html");
            }
        }
    }
}