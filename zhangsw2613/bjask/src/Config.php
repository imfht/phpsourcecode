<?php
/**
 * 配置文件类
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/28
 * Time: 14:11
 */

namespace Bjask;

/**
 * 配置文件加载类
 * @package Bjask
 * @copyright imageco.com.cn
 * @author zsw
 */
class Config
{
    const configPath = APP_NAME . '/Configs';
    const configFile = 'system.php';

    private static $config = [];
    private static $_loadFiles = [];
    private static $instance = null;

    /**
     * 导入配置文件
     * @param string $file_name
     * @return Config|null
     */
    public static function load($file_name = self::configFile): Config
    {
        $file = self::configPath . DIRECTORY_SEPARATOR . $file_name;
        self::$config = self::loadFile($file);
        if (isset(self::$config['ext_files']) && strlen(self::$config['ext_files']) > 0) {
            foreach (explode(',', self::$config['ext_files']) as $file) {
                self::$config = array_merge(self::loadFile(self::configPath . DIRECTORY_SEPARATOR . $file), self::$config);
            }
        }
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取内容
     * @param string $key
     * @return array|mixed|string
     */
    public function get($key = '')
    {
        if (empty($key)) {
            return self::$config;
        }
        return self::$config[$key] ?? '';
    }

    /**
     * 加载配置文件
     * @param $file
     * @return array|mixed
     */
    private static function loadFile($file)
    {
        if (!file_exists($file)) {
            return [];
        }
        if (!isset(self::$_loadFiles[$file])) {
            self::$_loadFiles[$file] = include $file;
        }
        return self::$_loadFiles[$file];
    }
}