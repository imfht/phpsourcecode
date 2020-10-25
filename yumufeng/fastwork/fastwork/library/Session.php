<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:06
 */

namespace fastwork;

use fastwork\facades\Cookie as FastCookie;
use fastwork\facades\Cache;

class Session
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    /**
     * Cookie前缀
     * @var string
     */
    public $sessionName = 'sessid';

    /**
     * Cache的前缀
     * @var string
     */
    protected $prefix = 'sessid_';
    /**
     * Session有效期
     * @var int
     */
    protected $expire = 0;
    /**
     * 是否初始化
     * @var bool
     */
    protected $init = null;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * 反射自动注入
     * @param Config $config
     * @return Session
     */
    public static function __make(Config $config)
    {
        return new static($config->pull('session'));
    }

    /**
     * session初始化
     * @access public
     * @param  array $config
     * @return Session
     */
    public function init(array $config = [])
    {
        $config = $config ?: $this->config;

        if (!empty($config['name'])) {
            $this->setSessionName($config['name']);
        }

        if (!empty($config['expire'])) {
            $this->expire = $config['expire'];
        }

        if (!empty($config['auto_start'])) {
            $this->start();
        } else {
            $this->init = false;
        }

        return $this;
    }

    /**
     * 启动session
     * @access public
     * @return void
     */
    public function start()
    {
        $sessionId = $this->getId();
        if (!$sessionId) {
            $this->regenerate();
        }
        $this->init = true;
    }

    /**
     * 获取session_id
     * @access public
     * @return string
     */
    public function getId()
    {
        return FastCookie::get($this->getSessionName()) ?: '';
    }

    /**
     * session_id设置
     * @access public
     * @param  string $id session_id
     * @param  int $expire Session有效期
     * @return void
     */
    public function setId($id, $expire = null)
    {
        FastCookie::set($this->getSessionName(), $id, $expire);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * session获取
     * @access public
     * @param  string $name session名称
     * @return mixed
     */
    public function get($name = '', $prefix = null)
    {
        empty($this->init) && $this->boot();

        $sessionId = $this->getId();

        if ($sessionId) {
            return $this->readSession($sessionId, $name);
        }
    }

    /**
     * session设置
     * @access public
     * @param  string $name session名称
     * @param  mixed $value session值
     * @return void
     */
    public function set($name, $value)
    {
        empty($this->init) && $this->boot();
        $sessionId = $this->getId();
        if (!$sessionId) {
            $sessionId = $this->regenerate();
        }
        if ($sessionId) {
            $this->setSession($sessionId, $name, $value);
        }
    }

    /**
     * 清空session数据
     * @access public
     * @return void
     */
    public function clear()
    {
        empty($this->init) && $this->boot();

        $sessionId = $this->getId();

        if ($sessionId) {
            $this->destroySession($sessionId);
        }
    }


    /**
     * 销毁session
     * @access public
     * @return void
     */
    public function destroy()
    {
        $sessionId = $this->getId();

        if ($sessionId) {
            $this->destroySession($sessionId);
        }

        $this->init = null;
    }


    /**
     * 判断session数据
     * @access public
     * @param  string $name session名称
     * @return bool
     */
    public function has($name)
    {
        empty($this->init) && $this->boot();
        $sessionId = $this->getId();

        if ($sessionId) {
            return $this->hasSession($sessionId, $name);
        }

        return false;
    }

    /**
     * 删除session数据
     * @access public
     * @param  string|array $name session名称
     * @return void
     */
    public function delete($name)
    {
        empty($this->init) && $this->boot();

        $sessionId = $this->getId();

        if ($sessionId) {
            $data = $this->getSession($sessionId);
            $redata = $this->deleteSession($sessionId, $name, $data);
            // 持久化session数据
            $this->writeSessionData($sessionId, $redata);
        }
    }

    /**
     * session获取并删除
     * @access public
     * @param  string $name session名称
     * @param  string|null $prefix 作用域（前缀）
     * @return mixed
     */
    public function pull($name, $prefix = null)
    {
        $result = $this->get($name, $prefix);

        if ($result) {
            $this->delete($name, $prefix);
            return $result;
        } else {
            return;
        }
    }

    /**
     * 添加数据到一个session数组
     * @access public
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key);

        if (is_null($array)) {
            $array = [];
        }

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * 删除session数据
     * @access protected
     * @param  string $sessionId session_id
     * @param  string|array $name session名称
     * @return void
     */
    protected function deleteSession($sessionId, $name, $data)
    {
        if (is_array($name)) {
            foreach ($name as $key) {
                $this->deleteSession($sessionId, $key, $data);
            }
        } elseif (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            unset($data[$name1][$name2]);
        } else {
            unset($data[$name]);
        }

        return $data;
    }

    /**
     * session设置
     * @access protected
     * @param  string $sessionId session_id
     * @param  string $name session名称
     * @param  mixed $value session值
     * @return void
     */

    protected function setSession($sessionId, $name, $value)
    {
        $data = $this->getSession($sessionId);
        if (strpos($name, '.')) {
            // 二维数组赋值
            list($name1, $name2) = explode('.', $name);
            $data[$name1][$name2] = $value;
        } else {
            $data[$name] = $value;
        }

        // 持久化session数据
        $this->writeSessionData($sessionId, $data);
    }

    protected function writeSessionData($sessionId, $data)
    {
        Cache::set($this->getPrefix() . $sessionId, $data, $this->expire);
    }


    /**
     * 从内存里读取session
     * @return mixed|null|void
     */
    protected function getSession($sessionId)
    {
        return Cache::get($this->getPrefix() . $sessionId);
    }

    /**
     * session获取
     * @access protected
     * @param  string $sessionId session_id
     * @param  string $name session名称
     * @return mixed
     */
    protected function readSession($sessionId, $name = '')
    {
        $value = !empty($this->getSession($sessionId)) ? $this->getSession($sessionId) : [];

        if (!is_array($value)) {
            $value = [];
        }

        if ('' != $name) {
            $name = explode('.', $name);

            foreach ($name as $val) {
                if (isset($value[$val])) {
                    $value = $value[$val];
                } else {
                    $value = null;
                    break;
                }
            }
        }

        return $value;
    }


    /**
     * 判断session数据
     * @access protected
     * @param  string $sessionId session_id
     * @param  string $name session名称
     * @return bool
     */
    protected function hasSession($sessionId, $name)
    {
        $value = !empty($this->getSession($sessionId)) ? $this->getSession($sessionId) : [];

        $name = explode('.', $name);

        foreach ($name as $val) {
            if (!isset($value[$val])) {
                return false;
            } else {
                $value = $value[$val];
            }
        }

        return true;
    }

    /**
     * session自动启动或者初始化
     * @access public
     * @return void
     */
    public function boot()
    {
        if (is_null($this->init)) {
            $this->init();
        }

        if (false === $this->init) {
            $this->start();
        }
    }

    /**
     * 生成session_id
     * @access public
     * @param  bool $delete 是否删除关联会话文件
     * @return string
     */
    public function regenerate($delete = false)
    {
        if ($delete) {
            $this->destroy();
        }
        $sessionId = sha1(microtime(true) . uniqid());

        $this->setId($sessionId);

        return $sessionId;
    }

    /**
     * 销毁session
     * @access protected
     * @param  string $sessionId session_id
     * @return void
     */
    protected function destroySession($sessionId)
    {

        Cache::rm($this->getPrefix() . $sessionId);

    }

    /**
     * @param string $sessionName
     */
    public function setSessionName(string $sessionName): void
    {
        $this->sessionName = $sessionName;
    }

    /**
     * @return string
     */
    public function getSessionName(): string
    {
        return $this->sessionName;
    }


    /**
     * 暂停session
     * @access public
     * @return void
     */
    public function pause()
    {
        $this->init = false;
    }
}