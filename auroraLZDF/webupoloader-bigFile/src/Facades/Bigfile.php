<?php

namespace AuroraLZDF\Bigfile\Facades;

use Illuminate\Support\Facades\Facade;

class Bigfile extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bigfile';
    }
}