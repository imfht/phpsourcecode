<?php

namespace Madphp\Cache;

class Util
{
    protected static $tmp = array();

    public static $disabled = false;

    public static $config = array(
        // blank for auto
        "storage" => "",
        // 0777 , 0666, 0644
        "default_chmod" => 0777,
        /*
         * Fall back when old driver is not support
         */
        "fallback" => "file",

        "securityKey" => "auto",
        "htaccess" => true,
        "path" => "",

        "memcache" => array(
            array("127.0.0.1", 11211, 1),
        ),

        "redis" => array(
            "host" => "127.0.0.1",
            "port" => "",
            "password" => "",
            "database" => "",
            "timeout" => ""
        ),

        "extensions" => array(),
    );

    private function __construct()
    {
        
    }

    /**
     * 获取实例|简单工厂
     * @param string $storage
     * @param array $config
     * @return mixed
     */
    public static function instance($storage = "", $config = array())
    {
        return Instance::get($storage, $config);
    }

    /**
     * 获取缓存存储路径
     * @param bool|false $skip_create_path
     * @param $config
     * @return string
     * @throws \Exception
     */
    public static function getPath($skip_create_path = false, $config)
    {

        if ($config['path'] != '') {
            $path = $config['path'];
        } elseif (defined('CACHE_PATH')) {
            $path = CACHE_PATH;
        } else {
            // revision 618
            if (self::isPHPModule()) {
                $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
                $path = $tmp_dir;
            } else {
                $path = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], "/") . '/' : rtrim(dirname(__FILE__), "/") . "/";
            }

            if (self::$config['path'] != "") {
                $path = $config['path'];
            }
        }

        // 从参数配置获取
        $securityKey = $config['securityKey'];
        if ($securityKey == "" || $securityKey == "auto") {
            // 从预定义配置获取
            $securityKey = self::$config['securityKey'];
            if ($securityKey == "auto" || $securityKey == "") {
                // 根据SERVER信息获取
                $securityKey = isset($_SERVER['HTTP_HOST']) ? ltrim(strtolower($_SERVER['HTTP_HOST']), "www.") : "default";
                $securityKey = preg_replace("/[^a-zA-Z0-9]+/", "", $securityKey);
            }
        }
        if ($securityKey != "") {
            $securityKey .= "/";
        }

        $full_path = rtrim($path, "/") . "/" . $securityKey;
        $full_pathx = md5($full_path);

        if ($skip_create_path == false && !isset(self::$tmp[$full_pathx])) {
            if (!file_exists($full_path) || !is_writable($full_path)) {
                if (!file_exists($full_path)) {
                    mkdirs($full_path, self::setChmodAuto($config));
                }
                if (!is_writable($full_path)) {
                    chmod($full_path, self::setChmodAuto($config));
                }
                if (!file_exists($full_path) || !is_writable($full_path)) {
                    throw new \Exception($full_path . " 需要可写权限");
                }
            }
            self::$tmp[$full_pathx] = true;
            self::htaccessGen($full_path, $config['htaccess']);
        }

        return $full_path;
    }

    public static function isPHPModule()
    {
        if (PHP_SAPI == "apache2handler") {
            return true;
        } else {
            if (strpos(PHP_SAPI, "handler") !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取文件模式
     * @param $config
     * @return int
     */
    public static function setChmodAuto($config)
    {
        if ($config['default_chmod'] == "" || is_null($config['default_chmod'])) {
            return 0777;
        } else {
            return $config['default_chmod'];
        }
    }

    /**
     * 创建 .htaccess 文件
     * @param $path
     * @param bool|true $create
     */
    protected static function htaccessGen($path, $create = true)
    {
        if ($create == true) {
            if (!is_writeable($path)) {
                try {
                    chmod($path, 0777);
                } catch (\Exception $e) {
                    throw new \Exception($path . " 需要可写权限");
                }
            }
            if (!file_exists(rtrim($path, '/') . "/" . ".htaccess")) {
                $text = "order deny, allow \r\ndeny from all \r\nallow from 127.0.0.1";

                $f = @fopen(rtrim($path, '/') . "/" . ".htaccess", "w+");
                if (!$f) {
                    throw new \Exception($path . " 需要 777 权限");
                }
                fwrite($f, $text);
                fclose($f);
            }
        }
    }

    protected static function getOS()
    {
        $os = array(
            "os" => PHP_OS,
            "php" => PHP_SAPI,
            "system" => php_uname(),
            "unique" => md5(php_uname() . PHP_OS . PHP_SAPI)
        );
        return $os;
    }

    public static function setup($name, $value = "")
    {
        if (is_array($name)) {
            self::$config = array_merge(self::$config, $name);
        } else {
            self::$config[$name] = $value;
        }
    }

    public static function isExistingDriver($class)
    {
        $namex = ucfirst(strtolower($class));
        if (!file_exists(dirname(__FILE__) . "/Drivers/" . $namex . ".php")) {
            return false;
        }

        require_once(dirname(__FILE__) . "/Drivers/" . $namex . ".php");
        $class = __NAMESPACE__ . "\\Drivers\\" . $namex;
        if (class_exists($class)) {
            return true;
        }

        return false;
    }
}