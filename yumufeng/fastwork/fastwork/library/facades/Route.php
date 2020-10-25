<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/4
 * Time: 10:37
 */

namespace fastwork\facades;


use fastwork\Facade;

/**
 * @method static Route get(string $route, Callable $callback)
 * @method static Route post(string $route, Callable $callback)
 */
class Route extends Facade
{

    protected static function getFacadeClass()
    {
        return 'route';
    }

}