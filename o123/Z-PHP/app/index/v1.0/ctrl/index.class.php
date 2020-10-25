<?php
namespace ctrl;

use root\base\ctrl;
use model\hello;

class index extends ctrl
{
    public static function index()
    {
        $model = new hello;
        $msg = $model->msg();
        echo $msg;
    }
}
