<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 23:27
 */

namespace fastwork\facades;


use fastwork\exception\HttpRuntimeException;
use fastwork\Facade;
use fastwork\Response;

/**
 * @see \fastwork\Error
 * @mixin \fastwork\Error
 * @method void render(Response $response, HttpRuntimeException $e) static
 * @method mixed report(\Throwable $e) static
 */
class Error extends Facade
{

    protected static function getFacadeClass()
    {
        return 'error';
    }
}