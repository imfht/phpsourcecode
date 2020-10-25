<?php
/**
 * @package     Session.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月18日
 */

namespace SlimCustom\Libs\Session;

use SlimCustom\Libs\Support\Arr;
use SlimCustom\Libs\App;
use SlimCustom\Libs\Contracts\Session\SessionInterface;
use Slim\Collection;

/**
 * Session
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class Session extends Collection implements SessionInterface
{
    /**
     * @var \SlimCustom\Libs\Session\Cache
     * @var \SlimCustom\Libs\Session\File
     */
    public $handler;
    
    /**
     * SessionId
     * 
     * @var string
     */
    protected $sessionid;
    
    /**
     * support drivers
     * 
     * @var array
     */
    protected $support = [
        'file' => \SlimCustom\Libs\Session\File::class,
        'cache' => \SlimCustom\Libs\Session\Cache::class,
    ];
    
    /**
     * 初始化session
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($this->handler()->start($config)->all());
        $this->sessionid = session_id();
    }
    
    /**
     * Returns the session ID.
     *
     * @return string The session ID
     */
    public function getId()
    {
        return $this->sessionid;
    }
    
    /**
     * Sets the session ID.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->sessionid = $id;
    }
    
    /**
     * Return All
     * 
     * {@inheritDoc}
     * @see \Slim\Collection::all()
     */
    public function all()
    {
        return $this->data;
    }
    
    /**
     * 读取session
     * 
     * {@inheritDoc}
     * @see \Slim\Collection::get()
     */
    public function get($key, $default = null)
    {
        $value = Arr::get($this->data, $key, $default);
        return is_null($value) ? value($default) : $value;
    }
    
    /**
     * 设置session
     * 
     * {@inheritDoc}
     * @see \Slim\Collection::set()
     */
    public function set($key, $value)
    {
        return $this->put($key, $value); 
    }
    
    /**
     * 写入session
     *
     * @param string $key
     * @param mix $value
     */
    public function put($key, $value)
    {
        return $this->data = Arr::set($this->data, $key, $value);
    }
    
    /**
     * Removes an attribute.
     *
     * @param string $name
     *
     * @return mixed The removed value or null when it does not exist
     */
    public function remove($name)
    {
        $this->data = Arr::except($this->data, $name);
        if (Arr::has($this->data, $name)) {
            return false;
        }
        return true;
    }
    
    /**
     * destroy session data
     *
     * @return boolean
     */
    public function clear()
    {
        return $this->handler->destroy();
    }
    
    /**
     * Checks if the session was started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return (session_status() == 2) ? true : false;
    }
    
    /**
     * get handler
     * 
     * @throws \Exception
     * @return \SlimCustom\Libs\Session\File
     * @return \SlimCustom\Libs\Session\Cache
     */
    public function handler()
    {
        if (! $this->handler) {
            $handler = config('session.driver', 'file');
            if (! isset($this->support[$handler])) {
                throw new \Exception('not support: ' . $handler);
            }
            $this->handler = App::di($this->support[$handler]);
        }
        return $this->handler;
    }
    
    /**
     * close
     *
     * @return boolean
     */
    public function close()
    {
        return $this->handler->close();
    }
    
    /**
     * Force the session to be saved and closed.
     *
     * This method is generally not required for real sessions as
     * the session will be automatically saved at the end of
     * code execution.
     */
    public function save()
    {
        $this->handler()->write($this->data);
        $this->close();
    }
    
    public function __destruct()
    {
        $this->save();
    }
}