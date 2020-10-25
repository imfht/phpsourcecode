<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-02 12:49:11
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-24 15:32:28
 */

namespace console\controllers;

use common\services\backend\NavService;
use Yii;
use yii\console\Controller;

// 使用示例： ./yii addons -addons=diandi_lottery -bloc_id=1 -store_id=3   job ninini

class AddonsController extends Controller
{
    public $addons;

    public $store_id;

    public $bloc_id;

    public function actions()
    {
        Yii::$app->service->commonGlobalsService->initId($this->bloc_id, $this->store_id, $this->addons);
        Yii::$app->service->commonGlobalsService->getConf($this->bloc_id);
    }

    public function options($actionID)
    {
        return ['addons', 'bloc_id', 'store_id'];
    }

    public function optionAliases()
    {
        return [
            'addons' => 'addons',
            'bloc_id' => 'bloc_id',
            'store_id' => 'store_id',
        ];
    }

    public function actionIndex($action, $param)
    {
        Yii::$app->getModule($this->addons)->$action($param);
    }

    public function actionCreatemenu()
    {
        NavService::addonsMens($this->addons);
    }
    
}
