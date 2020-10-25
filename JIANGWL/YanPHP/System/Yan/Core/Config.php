<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 */

namespace Yan\Core;


class Config
{
    /**
     * the loaded config options
     * @var array
     */
    protected static $_configItems = array();

    protected static $_isLoaded = array();

    /**
     * init function ,load autoload config files
     */
    public static function initialize()
    {
        $files = scandir(BASE_PATH . '/Config');
        foreach ($files as $file) {
            if (preg_match("/.*\.php$/", $file)) {
                self::loadConfig($file, true);
            }
        }
    }


    /**
     * @param string $key
     * @return array|bool|string
     */
    public static function get(string $key = '')
    {
        if (empty($key)) return self::$_configItems;
        return isset(self::$_configItems[$key]) ? self::$_configItems[$key] : false;
    }


    /**
     * Get all config items
     *
     * @return array
     */
    public static function getAll(): array
    {
        return self::$_configItems;
    }

    public static function set(string $key = '', string $value = ''): void
    {
        if (empty($key)) return;
        self::$_configItems[$key] = $value;
    }

    /**
     * to load config files, mark put in $isLoaded
     * @param string $file
     * @param bool $isInit Whether is the initial call
     * @return bool
     */
    public static function loadConfig(string $file = '', bool $isInit = false): bool
    {
        $file = str_replace('.php', '', $file) . '.php';

        if (empty($file)) {
            throwErr('Wrong file name', ReturnCode::SYSTEM_ERROR, Exception\RuntimeException::class);
        }

        $filePath = BASE_PATH . '/Config/' . $file;

        if (!file_exists($filePath)) {
            throwErr("File {$file} doesn't exists", ReturnCode::SYSTEM_ERROR, Exception\RuntimeException::class);
        } else {
            if (isset(self::$_isLoaded[$file])) {
                return true;
            }

            include $filePath;

            if (empty($config) || !is_array($config)) {
                return false;
            }

            self::$_isLoaded[$file] = true;
            self::$_configItems = array_merge(self::$_configItems, $config);
        }
        if (!$isInit) {
            Log::info("Load config file '{$file}'");
        }
        return true;
    }

}