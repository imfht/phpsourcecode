<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-11 17:41:27
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-31 17:25:05
 */


namespace backend\controllers\system;

use Yii;
use  backend\controllers\BaseController;
use common\models\DdRegion;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\web\Response;

/**
 *
 */
class IndexController extends BaseController
{
    public $enableCsrfValidation = false;
    // public $layout = false;

    public function actionIndex()
    {
        $csrfToken = Yii::$app->request->csrfToken;
        return $this->render('index', ['csrfToken' => $csrfToken]);
    }




    /**
     * @return string
     */
    public function actionChildcate()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $pid = Yii::$app->request->post('parent_id');
            $cates = DdRegion::findAll(['pid' => $pid]);
            return $cates;
        }
    }
}
