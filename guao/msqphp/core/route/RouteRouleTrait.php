<?php declare (strict_types = 1);
namespace msqphp\core\route;

trait RouteRouleTrait
{
    // 所有路由规则
    private static $roule = [];

    /**
     * 路由规则检测
     *
     * @param  string $value 待检查值
     * @param  string $key 规则键
     *
     * @return bool
     */
    private static function checkRoule(string &$value, string $key): bool
    {
        // 规则存在
        // 如果是string则正则,否则调用函数
        return isset(static::$roule[$key]) && (
            is_string(static::$roule[$key])
            ? 0 !== preg_match(static::$roule[$key], $value)
            : call_user_func_array(static::$roule[$key], [ & $value])
        );

    }

    /**
     * 添加路由规则
     * @param  string  $key   规则键
     * @param  string  $func  正则
     * @param  Closure $func  回调函数
     *     @example
     *         Route::addRoule(string ':all', function() : bool {
     *             return true;
     *         });
     */
    public static function addRoule(string $key, $func): void
    {
        (is_string($func) || $func instanceof \Closure) || static::exception('错误的路由规则');
        static::$roule[$key] = $func;
    }
}
