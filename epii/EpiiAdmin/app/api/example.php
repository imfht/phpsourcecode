<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2019/9/12
 * Time: 11:14 AM
 */

namespace app\api;


use epii\app\api;

class example extends api
{


    protected function doAuth(): bool
    {
        // TODO: Implement doAuth() method.
        return true;
    }

    //?api.php?app=example@test
    public function test()
    {
        $this->success(["user" => ["name" => "张三"]]);
    }
}