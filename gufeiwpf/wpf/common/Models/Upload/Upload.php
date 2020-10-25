<?php
namespace Wpf\Common\Models\upload;
class Upload extends \Phalcon\Mvc\Model{
    public static function createPaymentInstance($upload_type = 0,$config = null){
        switch($upload_type){
            case 0:
                return new \Wpf\Common\Models\Upload\Driver\Local($config);
            case 1:
                return new \Wpf\Common\Models\Upload\Driver\Qiniu($config);
        }
        
    }
}