<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-19 18:05:45
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-29 20:04:41
 */


namespace api\controllers;

use Yii;
use api\controllers\AController;
use yii\filters\VerbFilter;
use common\components\Upload;
use common\helpers\ResultHelper;
use yii\helpers\Json;
use yii\rest\ActiveController;


class StoreController extends AController
{
    public $modelClass = '';
    protected $authOptional = ['*'];
    
    public function actionInfo()
    {
        global $_GPC;
        $store_id = Yii::$app->params['store_id'];
        $store = Yii::$app->service->commonGlobalsService->getStoreDetail($store_id);
        
        if(!$store){
            return ResultHelper::json(400, '商户或不存在，请检查配置参数', $store);
            
        }
        
        return ResultHelper::json(200, '获取成功', $store);

    }
}