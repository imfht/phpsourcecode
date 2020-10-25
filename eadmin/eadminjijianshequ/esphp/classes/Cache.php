<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace esclass;

class Cache
{
    protected static $path = 0;

    function __construct()
    {

        self::$path = config('cache.path') . config('cache.prefix') . DS;

        if (!file_exists(config('cache.path'))) {

            mkdir(config('cache.path'));

        }

        if (!file_exists(self::$path)) {

            mkdir(self::$path);

        }
    }

    /**
     * 写入缓存
     *
     * @access public
     * @param string $name  缓存标识
     * @param mixed  $value 存储数据
     * @return boolean
     */
    public static function set($name, $value)
    {

        $name = md5($name);
        $data = serialize($value);
        $data = gzcompress($data, 3);

        file_put_contents(self::$path . $name . '.php', $data);

        return true;
    }

    /**
     * 读取缓存
     *
     * @access public
     * @param string $name    缓存标识
     * @param mixed  $default 默认值
     * @return mixed
     */
    public static function get($name, $default = false)
    {
        $name = md5($name);
        if (!is_file(self::$path . $name . '.php')) {
            return $default;
        }
        $content = file_get_contents(self::$path . $name . '.php');
        if (false !== $content) {
            $content = gzuncompress($content);
            $content = unserialize($content);

            return $content;
        } else {
            return $default;
        }

    }

    /**
     *清除缓存
     *
     * @access public
     * @param string $name    缓存标识
     * @param mixed  $default 默认值
     * @return mixed
     */
    public static function clear($name, $default = false)
    {
        $name = md5($name);

        unlink(self::$path . $name . '.php');

        return;
    }


}