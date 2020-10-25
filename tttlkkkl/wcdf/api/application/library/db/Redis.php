<?php
/**
 * redis连接类
 * Date: 16-10-16
 * Time: 上午1:59
 * author :李华 yehong0000@163.com
 */

namespace db;


class Redis
{
    protected        $handler = null;
    protected static $Obj;
    protected        $config  = [
        'host'       => '127.0.0.1', // redis主机
        'port'       => 6379, // redis端口
        'password'   => '', // 密码
        'select'     => 0, // 操作库
        'expire'     => 3600, // 有效期(秒)
        'timeout'    => 0, // 超时时间(秒)
        'persistent' => true, // 是否长连接
    ];

    private function __construct($config)
    {
        $sysConfig = \Yaf\Application::app()->getConfig()->redis->toArray();
        $config = is_array($sysConfig) ? array_merge($sysConfig, $config) : $config;
        $this->config = array_merge($this->config, $config);
        $this->open();
    }

    /**
     * 返回一个redis连接实例
     *
     * @param $config
     *
     * @return \Redis
     */
    static public function getInstance($config = [])
    {
        if (!self::$Obj) {
            self::$Obj = new self($config);
        }
        return self::$Obj->handler;
    }

    /**
     * 连接
     * @return bool
     * @throws \Exception
     */
    private function open()
    {
        // 检测php环境
        if (!extension_loaded('redis')) {
            throw new \Exception('not support:redis');
        }
        $this->handler = new \Redis;

        // 建立连接
        $func = $this->config['persistent'] ? 'pconnect' : 'connect';
        $this->handler->$func($this->config['host'], $this->config['port'], $this->config['timeout']);

        if ('' != $this->config['password']) {
            $this->handler->auth($this->config['password']);
        }
        return true;
    }
}