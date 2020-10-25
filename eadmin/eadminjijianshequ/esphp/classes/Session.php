<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------
namespace esclass;

class Session
{

    protected static $prefix = '';
    protected static $init = null;

    /**
     * session初始化
     *
     * @param array $config
     * @return void
     */
    public static function init(array $config = [])
    {
        if (empty($config)) {
            $config = config('session');
        }

        $isDoStart = false;

        // 启动session
        if (!empty($config['auto_start']) && PHP_SESSION_ACTIVE != session_status()) {
            ini_set('session.auto_start', 0);
            $isDoStart = true;
        }

        if (isset($config['prefix']) && (self::$prefix === '' || self::$prefix === null)) {
            self::$prefix = $config['prefix'];
        }
        if (isset($config['var_session_id']) && isset($_REQUEST[$config['var_session_id']])) {
            session_id($_REQUEST[$config['var_session_id']]);
        } elseif (isset($config['id']) && !empty($config['id'])) {
            session_id($config['id']);
        }

        if (isset($config['savePath'])) {
            session_save_path($config['savePath']);
        }

        if (isset($config['maxLifeTime'])) {
            ini_set('session.gc_maxlifetime', $config['maxLifeTime']);
            ini_set('session.cookie_lifetime', $config['maxLifeTime']);
        }
        if (isset($config['secure'])) {
            ini_set('session.cookie_secure', $config['secure']);
        }


        if ($isDoStart) {
            session_start();
            self::$init = true;
        } else {
            self::$init = false;
        }
    }

    /**
     * session自动启动或者初始化
     *
     * @return void
     */
    public static function boot()
    {
        if (is_null(self::$init)) {
            self::init();
        } elseif (false === self::$init) {
            if (PHP_SESSION_ACTIVE != session_status()) {
                session_start();
            }
            self::$init = true;
        }
    }

    /**
     * session获取
     *
     * @param string      $name   session名称
     * @param string|null $prefix 作用域（前缀）
     * @return mixed
     */
    public static function get($name = '', $prefix = null)
    {
        empty(self::$init) && self::boot();
        $prefix = !is_null($prefix) ? $prefix : self::$prefix;
        if ('' == $name) {
            // 获取全部的session
            $value = $prefix ? (!empty($_SESSION[$prefix]) ? $_SESSION[$prefix] : []) : $_SESSION;
        } elseif ($prefix) {
            // 获取session
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                $value = isset($_SESSION[$prefix][$name1][$name2]) ? $_SESSION[$prefix][$name1][$name2] : null;
            } else {
                $value = isset($_SESSION[$prefix][$name]) ? $_SESSION[$prefix][$name] : null;
            }
        } else {
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                $value = isset($_SESSION[$name1][$name2]) ? $_SESSION[$name1][$name2] : null;
            } else {
                $value = isset($_SESSION[$name]) ? $_SESSION[$name] : null;
            }
        }
        return $value;
    }

    /**
     * 启动session
     *
     * @return void
     */
    public static function start()
    {
        session_start();
        self::$init = true;
    }

    /**
     * session设置
     *
     * @param string      $name   session名称
     * @param mixed       $value  session值
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public static function set($name, $value = '', $prefix = null)
    {
        empty(self::$init) && self::boot();

        $prefix = !is_null($prefix) ? $prefix : self::$prefix;
        if (strpos($name, '.')) {
            // 二维数组赋值
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                $_SESSION[$prefix][$name1][$name2] = $value;
            } else {
                $_SESSION[$name1][$name2] = $value;
            }
        } elseif ($prefix) {
            $_SESSION[$prefix][$name] = $value;
        } else {
            $_SESSION[$name] = $value;
        }
    }


    /**
     * 销毁session
     *
     * @return void
     */
    public static function destroy()
    {
        if (!empty($_SESSION)) {
            $_SESSION = [];
        }
        session_unset();
        session_destroy();
        self::$init = null;
    }

    /**
     * 重新生成session_id
     *
     * @param bool $delete 是否删除关联会话文件
     * @return void
     */
    private static function regenerate($delete = false)
    {
        session_regenerate_id($delete);
    }

    /**
     * 暂停session
     *
     * @return void
     */
    public static function pause()
    {
        // 暂停session
        session_write_close();
        self::$init = false;
    }

    /**
     * 添加数据到一个session数组
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public static function push($key, $value)
    {
        $array = self::get($key);
        if (is_null($array)) {
            $array = [];
        }
        $array[] = $value;
        self::set($key, $array);
    }

    /**
     * 判断session数据
     *
     * @param string      $name session名称
     * @param string|null $prefix
     * @return bool
     */
    public static function has($name, $prefix = null)
    {
        empty(self::$init) && self::boot();
        $prefix = !is_null($prefix) ? $prefix : self::$prefix;
        if (strpos($name, '.')) {
            // 支持数组
            list($name1, $name2) = explode('.', $name);
            return $prefix ? isset($_SESSION[$prefix][$name1][$name2]) : isset($_SESSION[$name1][$name2]);
        } else {
            return $prefix ? isset($_SESSION[$prefix][$name]) : isset($_SESSION[$name]);
        }
    }

    /**
     * 删除session数据
     *
     * @param string|array $name   session名称
     * @param string|null  $prefix 作用域（前缀）
     * @return void
     */
    public static function delete($name, $prefix = null)
    {
        empty(self::$init) && self::boot();
        $prefix = !is_null($prefix) ? $prefix : self::$prefix;
        if (is_array($name)) {
            foreach ($name as $key) {
                self::delete($key, $prefix);
            }
        } elseif (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                unset($_SESSION[$prefix][$name1][$name2]);
            } else {
                unset($_SESSION[$name1][$name2]);
            }
        } else {
            if ($prefix) {
                unset($_SESSION[$prefix][$name]);
            } else {
                unset($_SESSION[$name]);
            }
        }
    }

    /**
     * 清空session数据
     *
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public static function clear($prefix = null)
    {
        empty(self::$init) && self::boot();
        $prefix = !is_null($prefix) ? $prefix : self::$prefix;
        if ($prefix) {
            unset($_SESSION[$prefix]);
        } else {
            $_SESSION = [];
        }
    }


}