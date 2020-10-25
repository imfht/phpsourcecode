<?php

namespace Bjask\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bjask\Scheduler
 */
class Scheduler extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'scheduler';
    }

}
