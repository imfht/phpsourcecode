<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Config\Config;
use Timo\Exception\CoreException;

/**
 * 会员处理类
 *
 * Class Session
 * @package Timo\Core
 */
class Session
{
    /**
     * session前缀
     *
     * @var string
     */
    protected static $prefix = '';

    /**
     * session是否开启
     *
     * @var bool
     */
    protected static $active = false;

    /**
     * 初始化，设置、开启session
     *
     * @param array $option
     * @throws CoreException
     */
    public static function init(array $option = [])
    {
        $config = Config::runtime('session');
        if (!empty($config)) {
            $config = array_merge($config, $option);
        }

        $isDoStart = false;

        // 启动session
        if (!empty($config['auto_start']) && PHP_SESSION_ACTIVE != session_status()) {
            //ini_set('session.auto_start', 0);
            $isDoStart = true;
        }

        ini_set('session.cookie_httponly', 1);

        if (isset($config['prefix'])) {
            static::$prefix = $config['prefix'];
        }
        if (isset($config['name'])) {
            session_name($config['name']);
        }
        if (isset($config['id']) && !empty($config['id'])) {
            session_id($config['id']);
        }
        if (isset($config['save_path'])) {
            session_save_path($config['save_path']);
        }
        if (isset($config['cookie_path'])) {
            ini_set('session.cookie_path', $config['cookie_path']);
        }
        if (isset($config['domain'])) {
            ini_set('session.cookie_domain', $config['domain']);
        }
        if (isset($config['expire'])) {
            ini_set('session.gc_maxlifetime', $config['expire']);
            ini_set('session.cookie_lifetime', $config['expire']);
        }
        if (isset($config['cache_limiter'])) {
            session_cache_limiter($config['cache_limiter']);
        }
        if (isset($config['cache_expire'])) {
            session_cache_expire($config['cache_expire']);
        }
        if (!empty($config['type'])) {
            // session驱动
            $class = '\\Timo\\Session\\' . ucwords($config['type']);

            // 检查驱动类
            if (!class_exists($class) || !session_set_save_handler(new $class($config), true)) {
                throw new CoreException('Error in init session handler');
            }
        }
        if ($isDoStart) {
            static::start();
        }
    }

    /**
     * 设置session
     *
     * @param string $name session名称
     * @param mixed $value session值
     * @param string|null $prefix 作用域（前缀）
     * @example set('user_id', 1008); set('u.uid', 1008);
     * @return void
     */
    public static function set($name, $value = '', $prefix = null)
    {
        !static::$active && self::init();
        $prefix = !is_null($prefix) ? $prefix : static::$prefix;
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
     * 获取session
     *
     * @param string $name session名称
     * @param string|null $prefix 作用域（前缀）
     * @return mixed
     */
    public static function get($name = '', $prefix = null)
    {
        !static::$active && self::init();
        $prefix = !is_null($prefix) ? $prefix : static::$prefix;
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
     * 删除session数据
     *
     * @param string $name session名称
     * @param string|null $prefix 作用域（前缀）
     * @return void
     */
    public static function delete($name, $prefix = null)
    {
        !static::$active && self::init();
        $prefix = !is_null($prefix) ? $prefix : static::$prefix;
        if (strpos($name, '.')) {
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
        !static::$active && self::init();
        $prefix = !is_null($prefix) ? $prefix : static::$prefix;
        if ($prefix) {
            unset($_SESSION[$prefix]);
        } else {
            $_SESSION = [];
        }
    }

    /**
     * 判断session数据
     *
     * @param string $name session名称
     * @param string|null $prefix
     * @return bool
     * @internal param mixed $value session值
     */
    public static function has($name, $prefix = null)
    {
        !static::$active && self::init();
        $prefix = !is_null($prefix) ? $prefix : static::$prefix;
        if (strpos($name, '.')) {
            // 支持数组
            list($name1, $name2) = explode('.', $name);
            return $prefix ? isset($_SESSION[$prefix][$name1][$name2]) : isset($_SESSION[$name1][$name2]);
        } else {
            return $prefix ? isset($_SESSION[$prefix][$name]) : isset($_SESSION[$name]);
        }
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
    }

    /**
     * 启动session
     *
     * @return void
     */
    public static function start()
    {
        session_start();
        static::$active = true;
    }

    /**
     * 销毁session
     *
     * @return void
     */
    public static function destroy()
    {
        !static::$active && self::init();
        $_SESSION = [];
        session_unset();
        session_destroy();
    }
}
