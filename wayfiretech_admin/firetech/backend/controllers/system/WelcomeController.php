<?php


namespace backend\controllers\system;

use Yii;
use  backend\controllers\BaseController;

class WelcomeController extends BaseController
{
    public $layout = "@backend/views/layouts/main-base";
    public function actionIndex()
    {
        $this->layout = "@backend/views/layouts/main";

        Yii::$app->params['plugins'] = 'shop';
        return $this->render('index');
    }

    public function actionSysai()
    {
        Yii::$app->params['plugins'] = 'sysai';

        return $this->render('index', ['plugins' => 'sysai']);
    }

    public function actionMember()
    {
        Yii::$app->params['plugins'] = 'member';
        return $this->render('index', ['plugins' => 'member']);
    }

    public function actionAimember()
    {
        Yii::$app->params['plugins'] = 'aimember';
        return $this->render('index', ['plugins' => 'aimember']);
    }
    public function actionGoods()
    {
        Yii::$app->params['plugins'] = 'goods';
        return $this->render('index', ['plugins' => 'goods']);
    }

    public function actionMarketing()
    {
        Yii::$app->params['plugins'] = 'marketing';
        return $this->render('index', ['plugins' => 'marketing']);
    }

    public function actionOrder()
    {
        yii::$app->params['plugins'] = 'order';
        return $this->render('index', ['plugins' => 'order']);
    }

    public function actionWxapp()
    {
        yii::$app->params['plugins'] = 'wxapp';
        return $this->render('index', ['plugins' => 'wxapp']);
    }

    public function actionPlugins()
    {
        yii::$app->params['plugins'] = 'plugins';
        return $this->render('index', ['plugins' => 'plugins']);
    }
    public function actionSystem()
    {
        $this->layout = "@backend/views/layouts/main";
        $plugins = yii::$app->request->get('plugins');
        return $this->render('system', ['plugins' => $plugins]);
    }
}
