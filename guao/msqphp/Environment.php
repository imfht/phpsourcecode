<?php declare (strict_types = 1);
namespace msqphp;

final class Environment
{
    // 框架版本
    const VENSION            = 2.0; // 运行模式
    private static $run_mode = '';

    // 所有目录存放
    private static $path = [
        'application' => '',
        'bootstrap'   => '',
        'config'      => '',
        'library'     => '',
        'public'      => '',
        'resources'   => '',
        'root'        => '',
        'storage'     => '',
        'test'        => '',
        'framework'   => '',
    ];
    private static $vendor_path = [];

    // 初始化框架环境
    public static function init(): void
    {
        static::initAutoLoader();

        core\loader\AILoader::useAiload('environment', 1000, function () {
            // 配置处理
            core\config\Config::init();
            // 错误处理
            core\wrong\Wrong::init();
            // 时区设置
            date_default_timezone_set(core\config\Config::get('framework.timezone'));
        }, [], static::getPath('storage') . 'framework/aiload_cache_file.php', true);
    }
    /**
     * loader静态类储存一个数组
     * 包括所有通过自动加载加载的文件(框架自带或者composer[需要改动代码])
     * 以实现智能加载
     * 所以在载入自动加载类前先载入对应文件
     */
    private static function initAutoLoader(): void
    {
        $framework_path = static::getPath('framework');
        require $framework_path . 'core/loader/AutoLoadRecord.php';
        if (COMPOSER_AUTOLOAD) {
            // 载入简易加载类文件
            //使用则载入composer自动加载类
            require static::getPath('root') . 'vendor/autoload.php';
        } else {
            // 载入完整加载类文件
            require $framework_path . 'core/loader/AutoLoader.php';
            core\loader\AutoLoader::register();
        }
    }

    // 获取当前运行环境
    public static function getRunMode(): string
    {
        if (static::$run_mode !== '') {
            return static::$run_mode;
        }
        switch (PHP_SAPI) {
            case 'cli':
                return static::$run_mode = 'cli';
            case 'cgi':
            case 'cgi-fcgi':
            case 'apache':
            case 'apache2filter':
            case 'apache2handler':
            default:
                return static::$run_mode = 'web';
        }
    }

    // 设置路径
    public static function setPath(array $path_config): void
    {
        foreach ($path_config as $name => $path) {
            // 存在或报错
            if (!is_dir($path)) {
                throw new \Exception('框架环境初始化错误,原因:' . $path . '目录不存在');
            }
            // 是目录则realpath
            isset(static::$path[$name]) && static::$path[$name] = realpath($path) . DIRECTORY_SEPARATOR;
        }
    }

    // 获取路径
    public static function getPath(string $name): string
    {
        if (!isset(static::$path[$name])) {
            throw new \Exception('目标路径无法获取' . $name);
        }
        return static::$path[$name];
    }

    public static function getVenderFilePath(string $class, string $name, string $type):  ? string
    {

        // 去类命名空间头msqphp和类名
        // 例:msqphp\base\dir\Dir ----> base\dir
        $namespace       = str_replace([strrchr($class, '\\'), 'msqphp\\'], '', $class);
        $file_path_right = strtr($namespace, '\\', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $name . '.php';
        foreach (static::$vendor_path as $file_path_left) {
            $file_path = $file_path_left . $file_path_right;
            if (is_file($file_path)) {
                return $file_path;
            }
        }
        return null;
    }
    public static function addVenderPath(string $path) : void
    {
        static::$vendor_path[] = realpath($path) . DIRECTORY_SEPARATOR;
    }
}
