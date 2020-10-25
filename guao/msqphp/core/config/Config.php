<?php declare (strict_types = 1);
namespace msqphp\core\config;

use msqphp\base\arr\Arr;
use msqphp\base\dir\Dir;
use msqphp\base\file\File;
use msqphp\Environment;

/**
 * 只是一个简单的配置读取,提供一个配置缓存功能.
 */
final class Config
{
    private static $config = [];

    private static function exception(string $message): void
    {
        throw new ConfigException('[配置错误]' . $message);
    }

    public static function init()
    {
        $config_cache_path = Environment::getPath('storage') . 'framework' . DIRECTORY_SEPARATOR . 'cache_config.php';

        HAS_CACHE || File::delete($config_cache_path);

        if (is_file($config_cache_path)) {
            static::$config = require $config_cache_path;
        } else {
            // 加载全部                              获得文件列表
            array_map([__CLASS__, 'loadConfigFIle'], Dir::getFileList(Environment::getPath('config'), true));
            File::write($config_cache_path, '<?php return ' . var_export(static::$config, true) . ';');
        }
    }

    public static function get( ? string $key = null)
    {
        return Arr::get(static::$config, $key);
    }

    public static function set( ? string $key, $value) : void
    {
        Arr::set(static::$config, $key, $value);
    }

    private static function loadConfigFIle(string $file) : void
    {
        is_file($file) || static::exception(sprintf('配置文件:%s , 无法加载,请检查目录或者文件是否存在', $file));
        // 以文件名称作为配置键.
        static::$config[pathinfo($file, PATHINFO_FILENAME)] = require $file;
    }
}
