<?php

namespace Common;

use SCH60\Kernel\App;

class AppCustomHelper{
    
    public static function isLogin(){
        $isLoginStatus = App::$app->request->session_get("isLoginFinal");
        if($isLoginStatus !== true){
            return false;
        }
        
        $expire = App::$app->request->session_get('isLoginFinal_until');
        if(!is_numeric($expire) || $_SERVER['REQUEST_TIME'] >= $expire){
            return false;
        }
        return true;
    }
    
}