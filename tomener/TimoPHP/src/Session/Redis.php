<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.timophp.com/
 */

namespace Timo\Session;


use Timo\Exception\CoreException;

class Redis extends \SessionHandler
{
    /**
     * @var \Redis
     */
    protected $handler = null;

    protected $config = [
        'host' => '127.0.0.1',  // redis主机
        'port' => 6379,         // redis端口
        'password' => '',       // 密码
        'expire' => 3600,       // 有效期(秒)
        'timeout' => 0,         // 超时时间(秒)
        'persistent' => true,   // 是否长连接
        'key_prefix' => '',   // session redis key前缀
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 打开session处理器
     *
     * @param string $save_path
     * @param string $session_id
     * @return bool
     * @throws CoreException
     */
    public function open($save_path, $session_id)
    {
        // 检测php环境
        if (!extension_loaded('redis')) {
            throw new CoreException('Not Support Redis in Redis Session Handler');
        }
        $this->handler = new \Redis;
        // 建立连接
        $func = $this->config['persistent'] ? 'pconnect' : 'connect';
        $this->config['timeout'] > 0 ?
            $this->handler->$func($this->config['host'], $this->config['port'], $this->config['timeout']) :
            $this->handler->$func($this->config['host'], $this->config['port']);
        if ('' != $this->config['password']) {
            $this->handler->auth($this->config['password']);
        }
        return true;
    }

    /**
     * 关闭Session
     *
     * @return bool
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->handler->close();
        $this->handler = null;
        return true;
    }

    /**
     * 读取Session
     *
     * @param string $session_id
     * @return bool|string
     */
    public function read($session_id)
    {
        return $this->handler->get($this->config['key_prefix'] . $session_id);
    }

    /**
     * 写入Session
     * 
     * @param string $session_id
     * @param string $session_data
     * @return bool
     */
    public function write($session_id, $session_data)
    {
        if ($this->config['expire'] > 0) {
            return $this->handler->setex($this->config['key_prefix'] . $session_id, $this->config['expire'], $session_data);
        } else {
            return $this->handler->set($this->config['key_prefix'] . $session_id, $session_data);
        }
    }

    /**
     * 删除Session
     *
     * @param string $session_id
     * @return int
     */
    public function destroy($session_id)
    {
        return $this->handler->del($this->config['key_prefix'] . $session_id);
    }

    /**
     * Session 垃圾回收
     *
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return true;
    }
}
