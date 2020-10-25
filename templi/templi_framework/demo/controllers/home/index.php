<?php

namespace  demo\controllers\home;
use framework\web\Controller;


/**
 * Created by PhpStorm.
 * User: david
 * Date: 15-8-30
 * Time: 下午7:09
 */
class index extends Controller
{
    public function actionDetail($id)
    {
        $this->assign('id', $id);
        $this->display();
    }
}