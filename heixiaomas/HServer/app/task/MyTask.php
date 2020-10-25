<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/4/10
 * Time: 12:44
 */

use HServer\core\task\HServerTask;

class MyTask extends HServerTask
{
    protected $time = 1;

    public function run()
    {
        echo "MyTask", "\n";
    }

}