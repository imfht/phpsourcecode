<?php
namespace backend\modules\mp\controllers;

class DefaultController extends \yeesoft\controllers\admin\BaseController {
    public function actionConfig() {
        return $this->renderIsAjax('config');
    }
}