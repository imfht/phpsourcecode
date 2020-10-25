<?php
/**
 * Created by PhpStorm.
 * User: imust
 * Date: 2017/1/6
 * Time: 10:17
 */

namespace App\Services\Facades;


use Illuminate\Support\Facades\Facade;

class MessageAlertFacade extends Facade
{
    protected static function getFacadeAccessor() { return 'MessageAlert'; }
}