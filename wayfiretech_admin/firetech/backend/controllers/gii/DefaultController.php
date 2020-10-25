<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-11 17:41:27
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-31 17:34:41
 */

namespace backend\controllers\gii;

use Yii;
use yii\base\Response;
use yii\gii\controllers\DefaultController as controller;

class DefaultController extends controller
{
    public $layout = "@backend/views/gii/layouts/main";

    //  /**
    //  * {@inheritdoc}
    //  */
    // public function beforeAction($action)
    // {
    //     echo '5465765';die;
    //     Yii::$app->response->format = Response::FORMAT_HTML;
    //     return parent::beforeAction($action);
    // }
    
    // public function actions()
    // {
    //     echo '456464';
    //     die;
    // }
}
