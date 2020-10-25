<?php
namespace ebcms;

use \ebcms\Curl;

class Server
{

    private static $config;

    // 初始化
    public static function init()
    {
        return self::$config = \ebcms\Config::get('system.server');
    }

    public static function api($name, $param=[])
    {

        !empty(self::$config) || self::init();
        $config = self::$config;

        $param['api'] = $name;

        $salt = md5(substr(\think\Config::get('safe_code'), 6) . time());
        $param['_auth'] = json_encode([
            'appid' => $config['appid'],
            'salt' => $salt,
            'authstr' => md5($config['appsecret'] . $salt),
            'from' => request()->root(true),
        ]);
        $param['_type'] = 'official';

        $res = Curl::post('https://www.ebcms.com/server/api/index', $param);
        $res = json_decode($res, true);

        return $res;
    }

    public static function store($api, $param=[])
    {

        $param['api'] = $api;
        $param['domain'] = str_replace('/index.php/', '/', request()->root(true).'/');

        $res = Curl::post('https://www.ebcms.com/store/api/index', $param);
        $res = json_decode($res, true);

        return $res;
    }
}