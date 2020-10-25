<?php
namespace app\common\api;

use TencentYoutuyun\Youtu;
use TencentYoutuyun\Conf;

class YoutuApi 
{
    public $appid;
    public $secretId;
    public $secretKey;
    public $userid;
    
    public function __construct()
    {
        include \Env::get('root_path') . 'vendor' . DS . 'Youtu_sdk' . DS . 'include.php';
        $this->appid = trim(setting('yt_appid'));
        $this->secretId = trim(setting('yt_secretid'));
        $this->secretKey = trim(setting('yt_secretkey'));
        $this->userid = trim(setting('yt_appid'));
        if (empty($this->appid) || empty($this->secretId) || empty($this->secretKey)) {
            exception('请在“系统设置”中配置优图接口');
            exit;
        } 
        Conf::setAppInfo($this->appid, $this->secretId, $this->secretKey, $this->userid,conf::API_YOUTU_END_POINT );
    }
    
    public function __call($apiName, $apiArgs)
    {  
        $uploadRet = call_user_func_array(['TencentYoutuyun\Youtu', $apiName], $apiArgs);        
        return $uploadRet;
    }
}
