<?php declare (strict_types = 1);
namespace msqphp\core\route;

trait RouteLimiteTrait
{
    /**
     * 如果匹配则调用函数
     *
     * @param  miexd   $???? 限制名称,支持数组(多匹配)
     * @param  Closure $func 调用函数
     * @param  Array   $args 函数参数
     *
     * @throws  RouteException
     *
     * @return  void
     */

    public static function limit($may, $truevalue, \Closure $func, array $args): void
    {
        static::$matched || (in_array($truevalue, (array) $may) && call_user_func_array($func, $args));
    }

    // 匹配分组,成功则调用对应函数
    public static function group(string $group, string $value, \Closure $func, array $args = []): void
    {
        static::limit($value, static::$category_info['group'][$group], $func, $args);
    }

    // 匹配语言,成功则调用对应函数
    public static function language(string $language, \Closure $func, array $args = []): void
    {
        static::limit($language, static::$category_info['language'], $func, $args);
    }

    // 匹配主题,成功则调用对应函数
    public static function theme(string $theme, \Closure $func, array $args = []): void
    {
        static::limit($theme, static::$category_info['theme'], $func, $args);
    }

    // SSL协议, 即https限制
    public static function ssl(\Closure $func, array $args = []): void
    {
        static::limit('https', static::getProtocol(), $func, $args);
    }
    public static function https(\Closure $func, array $args = []): void
    {
        static::limit('https', static::getProtocol(), $func, $args);
    }

    // 来自url限制
    public static function referer($referer, \Closure $func, array $args = []): void
    {
        static::limit($referer, static::getReferer(), $func, $args);
    }

    // ip限制
    public static function ip($ip, \Closure $func, array $args = []): void
    {
        static::limit($ip, static::getIp(), $func, $args);
    }

    // 端口限制
    public static function port($port, \Closure $func, array $args = []): void
    {
        static::limit($port, static::getPort(), $func, $args);
    }

    // 域名限制
    public static function domain($domain, \Closure $func, array $args = []): void
    {
        static::limit($domain, static::getDomain(), $func, $args);
    }

    // 后缀限制
    public static function extension($extension, \Closure $func, array $args = []): void
    {
        static::limit($extension, static::getExtension(), $func, $args);
    }
}
