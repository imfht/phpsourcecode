<?php

namespace app\api\controller;

use app\api\controller\UnauthorizedException;
use app\api\controller\Send;
use think\Exception;
use think\Request;
use think\Db;
use think\Cache;

class Oauth
{
    /**
     * 过期时间秒数
     *
     * @var int
     */
    public static $expires = 72000;

    /**
     * 生成签名
     * _字符开头的变量不参与签名
     */
    public function makeSign ($data = [],$app_secret = '')
    {   
        unset($data['version']);
        unset($data['signature']);
        foreach ($data as $k => $v) {
            
            if(substr($data[$k],0,1) == '_'){

                unset($data[$k]);
            }
        }
        return $this->_getOrderMd5($data,$app_secret);
    }

    /**
     * 计算ORDER的MD5签名
     */
    private function _getOrderMd5($params = [] , $app_secret = '') {
        ksort($params);
        $params['key'] = $app_secret;
        return strtolower(md5(urldecode(http_build_query($params))));
    }

}