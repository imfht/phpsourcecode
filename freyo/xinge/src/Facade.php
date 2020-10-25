<?php

namespace Freyo\Xinge;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
    /**
     * @return \Freyo\Xinge\Client
     */
    public static function android()
    {
        return app('xinge.android');
    }

    /**
     * @return \Freyo\Xinge\Client
     */
    public static function iOS()
    {
        return app('xinge.ios');
    }
}
