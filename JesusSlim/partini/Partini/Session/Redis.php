<?php

namespace Partini\Session;


class Redis implements SessionProviderInterface
{

    /**
     * Redis句柄
     */
    private $handler;
    private $get_result;

    /**
     * Redis constructor.
     */
    public function __construct($config)
    {
        if (!extension_loaded('redis')) {
            throw new \Exception('redis not support');
        }
        if (empty($options)) {
            $options = array(
                'host' => $config->get('SESSION_REDIS_HOST') ? : '127.0.0.1',
                'port' => $config->get('SESSION_REDIS_PORT') ? : 6379,
                'timeout' => $config->get('SESSION_CACHE_TIME') ? : false,
                'persistent' => $config->get('SESSION_PERSISTENT') ? : false,
                'auth' => $config->get('SESSION_REDIS_AUTH') ? : false,
            );
        }
        $options['host'] = explode(',', $options['host']);
        $options['port'] = explode(',', $options['port']);
        $options['auth'] = explode(',', $options['auth']);
        foreach ($options['host'] as $key => $value) {
            if (!isset($options['port'][$key])) {
                $options['port'][$key] = $options['port'][0];
            }
            if (!isset($options['auth'][$key])) {
                $options['auth'][$key] = $options['auth'][0];
            }
        }
        $this->options = $options;
        $expire = $config->get('SESSION_EXPIRE');
        $this->options['expire'] = isset($expire) ? (int)$expire : (int)ini_get('session.gc_maxlifetime');;
        $this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : $config->get('SESSION_PREFIX');
        $this->handler = new \Redis;
    }

    /**
     * 连接Redis服务端
     * @access public
     * @param bool $is_master : 是否连接主服务器
     */
    public function connect($is_master = true)
    {
        if ($is_master) {
            $i = 0;
        } else {
            $count = count($this->options['host']);
            if ($count == 1) {
                $i = 0;
            } else {
                $i = rand(1, $count - 1);    //多个从服务器随机选择
            }
        }
        $func = $this->options['persistent'] ? 'pconnect' : 'connect';
        try {
            if ($this->options['timeout'] === false) {
                $result = $this->handler->$func($this->options['host'][$i], $this->options['port'][$i]);
                if (!$result)
                    throw new \Exception('Redis Error', 100);
            } else {
                $result = $this->handler->$func($this->options['host'][$i], $this->options['port'][$i], $this->options['timeout']);
                if (!$result)
                    throw new \Exception('Redis Error', 101);
            }
            if ($this->options['auth'][$i]) {
                $result = $this->handler->auth($this->options['auth'][$i]);
                if (!$result) {
                    throw new \Exception('Redis Error', 102);
                }
            }
        } catch (\Exception $e) {
            exit('Error Message:' . $e->getMessage() . '<br>Error Code:' . $e->getCode() . '');
        }
    }

    /**
     * @param $savePath
     * @param $sessName
     * @return bool
     */
    public function open($savePath, $sessName)
    {
        return true;
    }

    /**
     * +----------------------------------------------------------
     * 关闭Session
     * +----------------------------------------------------------
     * @access public
     * +----------------------------------------------------------
     */
    public function close()
    {
        if ($this->options['persistent'] == 'pconnect') {
            $this->handler->close();
        }
        return true;
    }

    /**
     * @param $sessID
     * @return bool|string
     */
    public function read($sessID)
    {
        $this->connect(0);
        $this->get_result = $this->handler->get($this->options['prefix'] . $sessID);
        //读取session时，刷新有效期
        if(is_int($this->options['expire']) && $this->options['expire']){
            $this->handler->setex($sessID, $this->options['expire'], $this->get_result);
        }
        return $this->get_result;
    }

    /**
     * @param $sessID
     * @param $sessData
     * @return bool
     */
    public function write($sessID, $sessData)
    {
        if (!$sessData || $sessData == $this->get_result) {
            return true;
        }
        $this->connect(1);
        $expire = $this->options['expire'];
        $sessID = $this->options['prefix'] . $sessID;
        if (is_int($expire) && $expire > 0) {
            $result = $this->handler->setex($sessID, $expire, $sessData);
        } else {
            $result = $this->handler->set($sessID, $sessData);
        }
        return $result;
    }

    /**
     * +----------------------------------------------------------
     * 删除Session
     * +----------------------------------------------------------
     * @access public
     * +----------------------------------------------------------
     * @param string $sessID
    +----------------------------------------------------------
     */
    public function destroy($sessID)
    {
        $this->connect(1);
        return $this->handler->delete($this->options['prefix'] . $sessID);
    }

    /**
     * @param $sessMaxLifeTime
     * @return bool
     */
    public function gc($sessMaxLifeTime)
    {
        return true;
    }

    /**
     *
     */
    public function execute()
    {
        session_set_save_handler(
            array(&$this, "open"),
            array(&$this, "close"),
            array(&$this, "read"),
            array(&$this, "write"),
            array(&$this, "destroy"),
            array(&$this, "gc")
        );
    }

    public function __destruct()
    {
        if ($this->options['persistent'] == 'pconnect') {
            $this->handler->close();
        }
        session_write_close();
    }

}
