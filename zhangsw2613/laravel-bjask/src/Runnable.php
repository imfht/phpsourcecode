<?php
/**
 * Runnable接口类
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/12/19
 * Time: 13:20
 */

namespace Bjask;


interface Runnable
{
    public function prepare();
    public function run();
}