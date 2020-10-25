<?php

namespace Lxj\Yii2\Tars\Route;

class TarsRouteFactory
{
    public static function getRoute($routeName = '')
    {
        return new TarsRoute();
    }
}
