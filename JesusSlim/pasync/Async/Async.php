<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/11/3
 * Time: 下午4:44
 */

namespace Async;


class Async
{

    /**
     * 单例模式
     * @var
     */
    private  static  $_instance;

    //private标记的构造方法
    private function __construct(){
    }

    //创建__clone方法防止对象被复制克隆
    public function __clone(){
        trigger_error('Clone is not allow!',E_USER_ERROR);
    }

    //单例方法,用于访问实例的公共的静态方法
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private $conf = array(
        'host' => 'localhost',
        'port' => 8888,
        'handler_path' => '/handler.php',
        'param_name_of_class' => 'cls',
        'param_name_of_function' => 'act',
        'timeout' => 30
    );

    /**
     * init
     * @param string $host
     * @param int $port
     * @param string $handler_path
     * @param string $param_name_of_class
     * @param string $param_name_of_function
     * @param int $timeout
     * @return $this
     */
    public function init($host = 'localhost',$port = 8888,$handler_path = '/handler.php',$param_name_of_class = 'cls',$param_name_of_function = 'act',$timeout = 30){
        $this->conf = compact('host','port','handler_path','param_name_of_class','param_name_of_function','timeout');
        return $this;
    }

    /**
     * send an async mission
     * @param $class_name
     * @param $function_name
     * @param $params
     * @return bool
     */
    public function send($class_name,$function_name,$params = array()){
        $params[$this->conf['param_name_of_class']] = $class_name;
        $params[$this->conf['param_name_of_function']] = $function_name;
        $data = http_build_query($params);
        $fp = fsockopen($this->conf['host'], $this->conf['port'], $err_no, $err_msg, $this->conf['timeout']);
        if (!$fp) {
            return false;
        } else {
            $out = "POST ".$this->conf['handler_path']." HTTP/1.1\r\n";
            $out .= "Host:".$this->conf['host']."\r\n";
            $out .= "Content-type:application/x-www-form-urlencoded\r\n";
            $out .= "Content-length:".strlen($data)."\r\n";
            $out .= "Connection:close\r\n\r\n";
            $out .= $data;
            fputs($fp, $out);
            fclose($fp);
        }
        return true;
    }
}