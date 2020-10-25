<?php declare (strict_types = 1);
namespace msqphp\core\route;

use msqphp\core\traits;

final class Route
{
    // 万能静态call
    use traits\CallStatic;

    // 解析,规则
    use RouteParseTrait, RouteRouleTrait;

    // 分组,限制
    use RouteCategoryTrait, RouteLimiteTrait;

    // 方法,静态
    use RouteMatchTrait, RouteStaticTrait;

    // 当前处理的url
    private static $url = '';

    // 待处理路径
    private static $pending_path = [];

    // 当前命名空间
    private static $namespace = '\\app\\';

    // 是否匹配成功过
    private static $matched = false;

    // 异常抛出
    private static function exception(string $message): void
    {
        throw new RouteException($message);
    }

    // route运行
    public static function run(): void
    {
        static::parsePathQueryExtension();
        $procedure_file = \msqphp\Environment::getPath('application') . 'route.php';
        $rule_file      = \msqphp\Environment::getPath('application') . 'route_rule.php';
        is_file($procedure_file) || static::exception(printf('路由解析失败,原因:路由流程文件%s不存在', $procedure_file));
        is_file($rule_file) || static::exception(printf('路由解析失败,原因:路由规则文件%s不存在', $rule_file));
        require $rule_file;
        require $procedure_file;
    }

    // 构建并获取url常量
    public static function bulid(): string
    {
        $url = static::getProtocol() . '://' . static::getDomain() . '/' . static::$url;
        defined('__URL__') || define('__URL__', $url);
        return $url;
    }

    // 错误,即匹配失败
    public static function error(\Closure $func, array $args = []): void
    {
        static::$matched || call_user_func_array($func, $args);
    }
}
