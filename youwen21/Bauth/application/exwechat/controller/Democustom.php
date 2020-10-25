<?php

namespace app\exwechat\controller;

use youwen\exwechat\api\custom\custom;

/**
 * 客服案例
 */
class Democustom
{

    public function customList()
    {
        $class = new custom($_GET['token']);
        $ret = $class->customList();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

}
