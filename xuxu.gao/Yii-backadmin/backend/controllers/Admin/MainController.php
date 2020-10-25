<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/18
 * Time: 9:38
 */

namespace backend\controllers\Admin;


use backend\controllers\BaseController;

class MainController extends BaseController{

    /**
     * 后台的首页面
     */
    public function actionMain(){

        return $this->render('index');
    }

}