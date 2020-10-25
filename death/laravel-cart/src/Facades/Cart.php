<?php
/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:35
 */
namespace Tanwencn\Cart\Facades;

use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return \Tanwencn\Cart\CartInstance
     */
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
