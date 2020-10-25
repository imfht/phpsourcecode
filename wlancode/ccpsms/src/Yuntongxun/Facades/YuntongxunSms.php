<?php


namespace Yuntongxun\Facades;


use Illuminate\Support\Facades\Facade;
use Yuntongxun\Providers\YuntongxunSmsServiceProvider;

class YuntongxunSms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'yuntongxunsms';
    }
}