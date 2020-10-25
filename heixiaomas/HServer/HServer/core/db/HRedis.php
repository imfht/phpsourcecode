<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/2/21
 * Time: 13:52
 */

namespace HServer\core\db;
use HServer\config\Config;


class HRedis
{
    /**
     * 静态成品变量 保存全局实例
     */
    private static $_instance;

    /**
     * 单例模式
     */
    public static function getInstance()
    {
        $re = Config::getRedis();
        if ($re['flag']) {
            if (!isset(self::$_instance)) {
                $config=array(
                    'host' =>$re["host"],
                    'port' => $re["port"],
                    'password' => $re["password"]
                );
                $redis =new HRedis($config);
                self::$_instance = $redis;
            }
            return self::$_instance;
        }
    }



    //default config
    protected $_config = array(
        'host' => '127.0.0.1',
        'port' => '6379',
        'password' => null
    );
    //static connect
    protected static $_fp;

    public function __construct($config = array())
    {
        if (self::$_fp) {
            return;
        }
        $fp = fsockopen($config['host'], $config['port'], $errno, $errstr, 10);
        if (!$fp) {
            echo "redis connect failed.<br />\n";
            exit;
        } else {
            self::$_fp = $fp;
            if ($config['password']) {
                $this->_auth($config['password']);
            }
        }
    }

    //auth password
    protected function _auth($password)
    {
        $cmd = " auth $password \r\n";
        $result = $this->_sendCmd($cmd);
        return $result;
    }

    //get a key value
    public function get($key)
    {
        $check = $this->_exists($key);
        if ($check) {
            $cmd = " get $key \r\n";
            $result = $this->_sendCmd($cmd);
            return $result;
        } else {
            return null;
        }
    }

    protected function _exists($key)
    {
        $cmd = " exists $key \r\n";
        $result = $this->_sendCmd($cmd);
        return $result;
    }

    //set a key value
    public function set($key, $val)
    {
        $cmd = " set $key $val \r\n";
        $result = $this->_sendCmd($cmd);
        return $result;
    }

    //list
    public function lpush($list_name, $val)
    {
        $cmd = " lpush $list_name $val \r\n";
        $result = $this->_sendCmd($cmd);
        return $result;
    }

    //list
    public function rpush($list_name, $val)
    {
        $cmd = " rpush $list_name $val \r\n";
        $result = $this->_sendCmd($cmd);
        return $result;
    }

    //queue
    public function lpop($list_name)
    {
        $cmd = " lpop $list_name \r\n";
        $result = $this->_sendCmd($cmd);
        return $result;
    }

    //stack
    public function rpop($list_name)
    {
        $cmd = " rpop $list_name \r\n";
        $result = $this->_sendCmd($cmd);
        return $result;
    }

    //get range of a list
    public function lrange($list_name, $start, $end)
    {
        $cmd = " lrange $list_name $start $end \r\n";
        $result = $this->_sendCmd($cmd);
        return $result;
    }

    //get length of a list
    public function llen($list_name)
    {
        $cmd = " llen $list_name \r\n";
        $result = $this->_sendCmd($cmd);
        return $result;
    }

    //private,process return vals
    protected function _sendCmd($cmd)
    {
        $fp = self::$_fp;
        fwrite($fp, $cmd);
        $pre = fgets($fp, 128);

        if (substr($pre, 0, 1) == '*') {
            $line = substr($pre, 1);
            $len = 128;
            $result = array();

            for ($i = 1; $i <= $line * 2; $i++) {
                $content = fgets($fp, $len + 10);

                if ($i % 2 == 0) {
                    $result[] = $content;
                } else {
                    $len = substr($content, 1);
                }
            }
        } elseif (substr($pre, 0, 1) == '$') {
            $len = substr($pre, 1);
            $result = fgets($fp, $len + 10);

        } elseif (substr($pre, 0, 1) == '+') {
            $status = substr($pre, 1, 2);

            if ($status == 'OK') {
                $result = true;
            } else {
                $result = false;
            }
        } elseif (substr($pre, 0, 1) == ':') {
            $result = (int)substr($pre, 1);
        } else {
            echo $pre;
            $result = '';
        }

        return $result;
    }

}