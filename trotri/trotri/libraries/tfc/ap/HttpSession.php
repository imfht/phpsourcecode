<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

use tfc\ap\interfaces\SessionSaveHandler;

/**
 * HttpSession class file
 * HTTP会话管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: HttpSession.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
class HttpSession
{
    /**
     * 构造方法：初始化用户自定义会话处理接口
     * @param \tfc\ap\interfaces\SessionSaveHandler|null $saveHandler
     */
    public function __construct(SessionSaveHandler $saveHandler = null)
    {
        if ($saveHandler !== null) {
            $this->registerSaveHandler($saveHandler);
        }

        register_shutdown_function(array($this, 'close'));
    }

    /**
     * 打开会话任务
     * @return \tfc\ap\HttpSession
     * @throws RuntimeException 如果打开任务失败，抛出异常
     */
    public function open()
    {
        if ($this->getIsStarted()) {
            return $this;
        }

        @session_start();
        if ($this->getId() == '') {
            $message = 'HttpSession Start session failed';
            if (file_exists('error_get_last')) {
                $errors = error_get_last();
                if (isset($errors['message'])) {
                    $message .= ', ' . $errors['message'];
                }
            }
            throw new RuntimeException($message);
        }

        return $this;
    }

    /**
     * 关闭会话任务
     * @return \tfc\ap\HttpSession
     */
    public function close()
    {
        if ($this->getId() !== '') {
            @session_write_close();
        }

        return $this;
    }

    /**
     * 销毁所有的会话
     * @return \tfc\ap\HttpSession
     */
    public function destroy()
    {
        if ($this->getIsStarted()) {
            @session_unset();
            @session_destroy();
        }

        return $this;
    }

    /**
     * 判断会话是否已经被打开
     * @return boolean
     */
    public function getIsStarted()
    {
        return ($this->getId() !== '');
    }

    /**
     * 获取会话ID
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * 设置会话ID
     * @param string $value
     * @return \tfc\ap\HttpSession
     * @throws RuntimeException 如果会话已经被打开，则只能通过session_regenerate_id()方式设置ID
     */
    public function setId($value)
    {
        if ($this->getIsStarted()) {
            throw new RuntimeException(
                'HttpSession Session has already been started, to change the session ID call regenerateId().'
            );
        }

        session_id($value);
        return $this;
    }

    /**
     * 重置会话ID
     * @param boolean $deleteOldSession
     * @return \tfc\ap\HttpSession
     */
    public function regenerateID($deleteOldSession = true)
    {
        session_regenerate_id((boolean) $deleteOldSession);
        return $this;
    }

    /**
     * 获取会话名
     * @return string
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * 设置会话名
     * @param string $value
     * @return \tfc\ap\HttpSession
     * @throws RuntimeException 如果会话已经被打开，则不允许重新设置会话名
     * @throws ErrorException 如果会话名中有英文字母和数字之外的字符，抛出异常
     */
    public function setName($value)
    {
        if ($this->getIsStarted()) {
            throw new RuntimeException(sprintf(
                'HttpSession Cannot set session name "%s" after a session has already started.', $value
            ));
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
            throw new ErrorException(sprintf(
                'HttpSession Name "%s" provided contains invalid characters, must be alphanumeric only.', $value
            ));
        }

        session_name($value);
        return $this;
    }

    /**
     * 获取会话保存路径
     * @return string
     */
    public function getSavePath()
    {
        return session_save_path();
    }

    /**
     * 设置会话保存路径
     * @param string $value
     * @return \tfc\ap\HttpSession
     * @throws RuntimeException 如果会话已经被打开，则不允许重新设置会话保存路径
     * @throws ErrorException 如果设置的目录地址不存在，抛出异常
     */
    public function setSavePath($value)
    {
        if ($this->getIsStarted()) {
            throw new ErrorException(sprintf(
                'HttpSession Cannot set session save path "%s" after a session has already started.', $value
            ));
        }

        if (is_dir($value)) {
            session_save_path($value);
            return $this;
        }

        throw new ErrorException(sprintf(
            'HttpSession Save Path "%s" is not a valid directory.', $value
        ));
    }

    /**
     * 判断Session是否会自动开启
     * @return boolean
     */
    public function getAutoStart()
    {
        return (ini_get('session.auto_start') == '1');
    }

    /**
     * 获取所有的会话
     * @return array
     */
    public function toArray()
    {
        return $_SESSION;
    }

    /**
     * 获取会话数据量
     * @return integer
     */
    public function count()
    {
        return count($_SESSION);
    }

    /**
     * 获取会话中所有的键
     * @return array
     */
    public function getKeys()
    {
        return array_keys($_SESSION);
    }

    /**
     * 通过会话键获取值，如果都找不到，返回默认值
     * @param mixed $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        return $this->has($key) ? $_SESSION[$key] : $defaultValue;
    }

    /**
     * 添加会话，如果键已经存在，替换老值
     * @param mixed $key
     * @param mixed $value
     * @return \tfc\ap\HttpSession
     */
    public function add($key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * 移除会话
     * @param mixed $key
     * @return mixed
     */
    public function remove($key)
    {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }

        return null;
    }

    /**
     * 移除所有的会话
     * @return \tfc\ap\HttpSession
     */
    public function clear()
    {
        foreach ($this->getKeys() as $key) {
            unset($_SESSION[$key]);
        }

        return $this;
    }

    /**
     * 判断键在会话中是否存在
     * @param mixed $key
     * @return boolean
     */
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * 获取PHP INI中设置的SESSION过期时间，默认：1440s
     * @return integer
     */
    public function getTimeout()
    {
        return (int) ini_get('session.gc_maxlifetime');
    }

    /**
     * 设置PHP INI中的SESSION过期时间，默认：1440s
     * @param integer $maxLifeTime
     * @return \tfc\ap\HttpSession
     */
    public function setTimeout($maxLifeTime)
    {
        ini_set('session.gc_maxlifetime', (int) $maxLifeTime);
        return $this;
    }

    /**
     * 设置用户自定义会话处理接口
     * @param \tfc\ap\interfaces\SessionSaveHandler $saveHandler
     * @return void
     */
    public function registerSaveHandler(SessionSaveHandler $saveHandler)
    {
        session_set_save_handler(
            array($saveHandler, 'open'),
            array($saveHandler, 'close'),
            array($saveHandler, 'read'),
            array($saveHandler, 'write'),
            array($saveHandler, 'destroy'),
            array($saveHandler, 'gc')
        );
    }
}
