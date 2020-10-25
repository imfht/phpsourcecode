<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.timophp.com/
 */

namespace Timo\Session;


use Timo\Exception\CoreException;

class Memcached extends \SessionHandler
{
    /**
     * @var \Memcached
     */
    protected $handler = null;

    protected $config  = [
        'host'          => '127.0.0.1', // memcache主机
        'port'          => 11211,       // memcache端口
        'weight'        => 1,           // 权重
        'expire'        => 3600,        // session有效期
        'timeout'       => 0,           // 连接超时时间（单位：毫秒）
        'key_prefix'  => '',          // memcache key前缀
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 关闭Session
     * @return bool
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->handler->quit();
        $this->handler = null;
        return true;
    }

    /**
     * 销毁session
     *
     * @param string $session_id
     * @return bool
     */
    public function destroy($session_id)
    {
        return $this->handler->delete($this->config['key_prefix'] . $session_id);
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
        if (!extension_loaded('memcached')) {
            throw new CoreException('Not Support Memcached');
        }
        $this->handler = new \Memcached;
        // 设置连接超时时间（单位：毫秒）
        if ($this->config['timeout'] > 0) {
            $this->handler->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $this->config['timeout']);
        }
        // 支持集群
        $hosts = explode(',', $this->config['host']);
        $ports = explode(',', $this->config['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        $servers = [];
        foreach ((array) $hosts as $i => $host) {
            $servers[] = [$host, (isset($ports[$i]) ? $ports[$i] : $ports[0]), 1];
        }
        $this->handler->addServers($servers);
        return true;
    }

    /**
     * 读取Session
     *
     * @param string $session_id
     * @return mixed
     */
    public function read($session_id)
    {
        return $this->handler->get($this->config['session_name'] . $session_id);
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
        return $this->handler->set($this->config['key_prefix'] . $session_id, $session_data, $this->config['expire']);
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
