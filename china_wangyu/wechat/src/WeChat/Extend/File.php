<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Extend;

/**
 * Class File 微信存储类
 * @package wechat\lib
 */
class File
{

    /**
     * 定义常量 / 路径连接符
     */
    private static $ext = '/';

    /**
     * 存储对象文件，可扩展
     * @param string $var
     * @param array $val
     * @return null
     */
    public static function param(string $var, array $val = [])
    {
        $file_path = self::mkdir('param');
        $fileCont = json_decode(file_get_contents($file_path), true);
        if(empty($fileCont) and empty($val)) return null;
        if(!empty($val) and !empty($var)){
            $val['time'] = time();
            $fileCont[$var] = $val;
            file_put_contents($file_path,json_encode($fileCont));
        }
        if(!empty($val) and empty($var)){
            if ($fileCont[$var]['time'] - time() <= 7100){
                unset($fileCont[$var]['time']);
                if (!empty($fileCont[$var])) return $fileCont[$var];
            }
            return null;
        }
    }

    /**
     * 支付日志
     * @param string $type
     * @param array $param
     * @return mixed
     */
    public static function paylog(string $type = 'wechat', array $param = [])
    {
        $file_path = self::mkdir('wechat');
        if (!empty($type) and empty($param)) {
            return json_decode(file_get_contents($file_path), true);
        }
        $data = '['.date('Y-m-d H:i:s').'] => '.json_encode($param) . PHP_EOL;
        file_put_contents($file_path, $data, FILE_APPEND);
    }



    /**
     * 创建日志类型文件
     * @param string $type
     * @return string
     */
    private static function mkdir(string $type = 'param')
    {
        $file_dir = dirname(__FILE__) . static::$ext . 'log' ;
        (!is_dir($file_dir)) && mkdir($file_dir, 0755);
        $file_dir .=  static::$ext . date('Y-m-d-H') . static::$ext;
        if ($type == 'param') {
            $file_dir = dirname(__FILE__) . static::$ext . 'log' . static::$ext . 'param' . static::$ext;
        }

        $file_name = $type . '.log';
        (!is_dir($file_dir)) && mkdir($file_dir, 0755);

        if (!is_file($file_dir . $file_name)) {
            file_put_contents($file_dir . $file_name, '');
        }
        return $file_dir . $file_name;
    }
}
