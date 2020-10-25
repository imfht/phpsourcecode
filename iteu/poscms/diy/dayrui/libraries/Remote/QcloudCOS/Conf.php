<?php

require_once FCPATH . 'dayrui/libraries/Remote/QcloudCOS/Auth.php';
require_once FCPATH . 'dayrui/libraries/Remote/QcloudCOS/Cosapi.php';
require_once FCPATH . 'dayrui/libraries/Remote/QcloudCOS/Http.php';

class Conf
{
    const PKG_VERSION = '1.0.0'; 

    const API_IMAGE_END_POINT = 'http://web.image.myqcloud.com/photos/v1/';
    const API_VIDEO_END_POINT = 'http://web.video.myqcloud.com/videos/v1/';
    const API_COSAPI_END_POINT = 'http://web.file.myqcloud.com/files/v1/';
    //请到http://console.qcloud.com/cos去获取你的appid、sid、skey
    public static $APPID;
    public static $SECRET_ID;
    public static $SECRET_KEY;

    public static function init($APPID, $SECRET_ID, $SECRET_KEY) {
        self::$APPID = $APPID;
        self::$SECRET_ID = $SECRET_ID;
        self::$SECRET_KEY = $SECRET_KEY;
    }

    public static function getUA() {
        return 'QcloudPHP/'.self::PKG_VERSION.' ('.php_uname().')';
    }
}


//end of script
