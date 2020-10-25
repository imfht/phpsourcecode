<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2019/9/12
 * Time: 11:14 AM
 */

namespace app\admin;

class example extends \epii\admin\center\admin_center_controller
{

    //index.php?app=example@index
    public function index()
    {
        $this->assign("user", ["name" => "张三"]);
        $this->display();
    }
}