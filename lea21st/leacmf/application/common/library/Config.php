<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/12/31
 * Time: 14:25
 */

namespace app\common\library;

use think\Db;
use think\facade\Cache;

class Config
{
    //获取配置
    public static function config()
    {
        $config = Cache::remember('sys:cache:config', function () {
            return self::lists();
        });
        \think\facade\Config::set($config, 'param');
    }

    /**
     * 获取数据库中的配置列表
     * @return array 配置数组
     */
    public static function lists()
    {
        $data   = Db::name('config')->field('type,name,value')->select();
        $config = [];
        if ($data && is_array($data)) {
            foreach ($data as $value) {
                $config[$value['name']] = self::parse($value['type'], $value['value']);
            }
        }

        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param $type
     * @param $value
     * @return array|array[]|false|string[]
     */
    private static function parse($type, $value)
    {
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = [];
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }

    // 分析枚举类型配置值 格式 a:名称1,b:名称2
    public static function parse_config_attr($string)
    {
        $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
        if (strpos($string, ':')) {
            $value = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }
}