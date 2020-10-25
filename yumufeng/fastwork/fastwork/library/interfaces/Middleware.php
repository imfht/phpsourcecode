<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/7
 * Time: 10:05
 */

namespace fastwork\interfaces;

use fastwork\Request;

interface MiddleWare
{
    public function handle(Request $request, \Closure $next);
}