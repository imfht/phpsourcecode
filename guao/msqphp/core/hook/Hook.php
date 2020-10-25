<?php declare (strict_types = 1);
namespace msqphp\core\hook;

final class Hook
{

    private static $hook_list = [];

    public static function add(string $name, $callable, bool $is_before = true, bool $first = false)
    {
        is_callable($callable) || static::exception('不是一个callable变量');

        isset(self::$tags[$tag]) || self::$tags[$tag] = ['before' => [], 'after' => []];
        if ($first) {
            array_unshift($is_before ? self::$tags[$tag]['before'] : self::$tags[$tag]['after'], $callable);
        } else {
            array_push($is_before ? self::$tags[$tag]['before'] : self::$tags[$tag]['after'], $callable);
        }
    }
    public static function remove(string $name, $callable)
    {
        is_callable($callable) || static::exception('不是一个callable变量');
        foreach (static::$hook_list[$name]['before'] as $key => $value) {
            if ($value === $callable) {
                unset(static::$hook_list[$name]['before'][$key]);
            }
        }
        foreach (static::$hook_list[$name]['after'] as $key => $value) {
            if ($value === $callable) {
                unset(static::$hook_list[$name]['after'][$key]);
            }
        }
    }
    public static function exec(string $name, $callable, array $args = [])
    {
        is_callable($callable) || static::exception('不是一个callable变量');
        foreach (static::$hook_list[$name]['before'] as $value) {
            call_user_func_array($value, []);
        }
        call_user_func_array($callable, $args);
        foreach (static::$hook_list[$name]['after'] as $value) {
            call_user_func_array($value, []);
        }
    }

}
