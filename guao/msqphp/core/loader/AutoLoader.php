<?php declare (strict_types = 1);
namespace msqphp\core\loader;

final class AutoLoader
{
    public static function register(): void
    {
        spl_autoload_register([__CLASS__, 'handler'], false, true);
    }

    public static function unregister(): void
    {
        spl_autoload_unregister([__CLASS__, 'handler']);
    }

    // 自动加载函数
    public static function handler(string $class_name): bool
    {
        // 类的顶级命名空间为
        switch (strstr($class_name, '\\', true)) {
            // 框架类相关
            case 'msqphp':
                // 框架中是否存在
                $file = \msqphp\Environment::getPath('framework') . str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 7)) . '.php';
                // 是否为用户自定义扩展
                is_file($file) || $file = \msqphp\Environment::getPath('library') . str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 7)) . '.php';
                return static::includeFile($file);
            // 用户应用文件
            case 'app':
                $file = \msqphp\Environment::getPath('application') . str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 4)) . '.php';
                return static::includeFile($file);
            // 用户测试类文件
            case 'test':
                $file = \msqphp\Environment::getPath('test') . str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 4)) . '.php';
                return static::includeFile($file);
            default:
                return false;
        }
    }

    // 加载文件函数,存在加载,并记录智能加载文件列表中,否侧false
    private static function includeFile(string $file): bool
    {
        if (is_file($file)) {
            AutoLoadRecord::record($file);
            include $file;
            return true;
        } else {
            return false;
        }
    }
}
