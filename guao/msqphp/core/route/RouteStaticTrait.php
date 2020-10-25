<?php declare (strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;

trait RouteStaticTrait
{
    public static function static(\Closure $func, $args = []): void {
        // 未匹配,直接返回
        if (static::$matched) {
            return;
        }
        // 调用闭包函数
        call_user_func_array($func, $args);
        // 未匹配成功或者不支持静态,则不属于static路由闭包函数包括范围,返回
        if (!static::$matched || !HAS_STATIC) {
            return;
        }
        $content = '<?php
// 加载基础文件
require \'' . \msqphp\Environment::getPath('bootstrap') . 'framework/base_app.php\';
require \'' . \msqphp\Environment::getPath('bootstrap') . 'framework/function.php\';
require \'' . \msqphp\Environment::getPath('bootstrap') . 'framework/user.php\';
// 初始化环境
\msqphp\Environment::init();
\msqphp\App::init();

// 控制器加路由开始时间
define(\'ROUTE_START\', microtime(true));

// 路由直接运行
\msqphp\core\route\Route::initStaticEnvironment(' . var_export(static::getStaticInfo(), true) . ');
\msqphp\core\route\Route::runStaticFunc();
// 控制器加路由结束时间
define(\'ROUTE_END\', microtime(true));';
        static::writeStaticFile(static::getStaticPath(), $content, 3600);

    }

    // 获取静态路径
    public static function getStaticPath(): string
    {
        $path = trim(strtr(static::getPath(), '/', DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

        empty($path) || $path .= DIRECTORY_SEPARATOR;

        return \msqphp\Environment::getPath('public') . $path . 'index.php';
    }
    /**
     * 写入静态文件
     * @param   string  $path     路径
     * @param   string  $content  内容
     * @param   int     $expire   过期时间
     * @return  void
     */
    public static function writeStaticFile(string $path, string $content, int $expire = 3600): void
    {
        if ($expire < 1) {return;}
        $content = '<?php if (time() >' . (string) (time() + $expire) . ') {require \'' . \msqphp\Environment::getPath('public') . 'server.php\';exit;}?>' . $content;
        base\file\File::write($path, $content);
    }
    /**
     * 设置路由信息
     *
     * @param array $info = [
     * ]
     */
    public static function initStaticEnvironment(array $info): void
    {
        include \msqphp\Environment::getPath('application') . 'route_rule.php';
        static::$category_info = $info['category_info'];
        static::$method_info   = $info['method_info'];
        static::$url           = $info['url'];
        static::$namespace     = $info['namespace'];
        foreach (static::$category_info['constant'] as $key => $value) {
            defined($key) || define($key, $value);
        }
        // 解析路径和参数
        static::parsePathQueryExtension();
    }
    public static function runStaticFunc(): void
    {
        $method_info = static::$method_info;
        static::checkMethod($method_info['method']);
        static::checkCondition($method_info['condition']);
        define('USER_FUNC_START', microtime(true));
        $class_name = $method_info['function']['class'];
        $method     = $method_info['function']['method'];
        $query      = $method_info['function']['query'];
        $args       = $method_info['function']['args'];
        call_user_func_array([new $class_name, $method], static::getArgsByQuery($query, $args));
        define('USER_FUNC_END', microtime(true));
    }
    public static function getStaticInfo(): array
    {
        return [
            'method_info'   => static::$method_info,
            'category_info' => static::$category_info,
            'url'           => static::$url,
            'namespace'     => static::$namespace,
        ];
    }
}
