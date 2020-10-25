<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/4/10
 * Time: 13:25
 */

namespace HServer\core\task;

use HServer\core\db\HDb;
use HServer\core\db\HRedis;
use HServer\core\db\HServerDB;

/**
 * Class HServerTask
 * @package HServer\core\task
 */
abstract class HServerTask extends HServerDB
{
    protected $time;

    public abstract function run();

}