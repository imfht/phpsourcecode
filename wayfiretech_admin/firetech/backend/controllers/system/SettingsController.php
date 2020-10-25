<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-28 23:43:29
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-09-07 11:09:24
 */


namespace backend\controllers\system;

use  backend\controllers\BaseController;
use common\helpers\ResultHelper;
use common\models\forms\ClearCache;
use Yii;

/**
 * Class SiteController
 *
 * @package app\controllers
 */
class SettingsController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'baidu' => [
                'class' => \yii2mod\settings\actions\SettingsAction::class,
                'view' => 'baidu',
                'successMessage' => '保存成功',
                'prepareModel' => 'common\models\Setting',
                // also you can use events as follows:
                'on beforeSave' => function ($event) {
                    // your custom code
                },
                'on afterSave' => function ($event) {
                    // your custom code
                },
                'modelClass' => \common\models\forms\Baidu::class,
            ],
            'wxapp' => [
                'class' => \yii2mod\settings\actions\SettingsAction::class,
                'view' => 'wxapp',
                'successMessage' => '保存成功',

                // also you can use events as follows:
                'on beforeSave' => function ($event) {
                    // your custom code
                },
                'on afterSave' => function ($event) {
                    // your custom code
                },
                'modelClass' => \common\models\forms\Wxapp::class,
            ],
            'wechat' => [
                'class' => \yii2mod\settings\actions\SettingsAction::class,
                'view' => 'wechat',
                'successMessage' => '保存成功',

                // also you can use events as follows:
                'on beforeSave' => function ($event) {
                    // your custom code
                },
                'on afterSave' => function ($event) {
                    // your custom code
                },
                'modelClass' => \common\models\forms\Wechat::class,
            ],
            'wechatpay' => [
                'class' => \yii2mod\settings\actions\SettingsAction::class,
                'successMessage' => '保存成功',
                'view' => 'wechatpay',
                // also you can use events as follows:
                'on beforeSave' => function ($event) {
                    // your custom code
                },
                'on afterSave' => function ($event) {
                    // your custom code
                },
                'modelClass' => \common\models\forms\Wechatpay::class,
            ],
            'weburl' => [
                'class' => \yii2mod\settings\actions\SettingsAction::class,
                'successMessage' => '保存成功',
                'view' => 'weburl',
                'prepareModel' => 'common\models\Setting',
                // also you can use events as follows:
                'on beforeSave' => function ($event) {
                    // your custom code
                },
                'on afterSave' => function ($event) {
                    // your custom code
                },
                'modelClass' => \common\models\forms\Weburl::class,
            ],
            'sms' => [
                'class' => \yii2mod\settings\actions\SettingsAction::class,
                'successMessage' => '保存成功',
                'view' => 'sms',
                // also you can use events as follows:
                'on beforeSave' => function ($event) {
                    // your custom code
                },
                'on afterSave' => function ($event) {
                    // your custom code
                },
                'modelClass' => \common\models\forms\Sms::class,
            ],
            'email'=>[
                'class' => \yii2mod\settings\actions\SettingsAction::class,
                'successMessage' => '保存成功',
                'view' => 'email',
                // also you can use events as follows:
                'on beforeSave' => function ($event) {
                    // your custom code
                },
                'on afterSave' => function ($event) {
                    // your custom code
                },
                'modelClass' => \common\models\forms\Email::class,
            ],
            'map'=>[
                'class' => \yii2mod\settings\actions\SettingsAction::class,
                'successMessage' => '保存成功',
                'view' => 'map',
                // also you can use events as follows:
                'on beforeSave' => function ($event) {
                    // your custom code
                },
                'on afterSave' => function ($event) {
                    // your custom code
                },
                'modelClass' => \common\models\forms\Map::class,
            ],
        ];
    }


    /**
     * 清理缓存
     *
     * @return string
     */
    public function actionClearCache()
    {
        // $this->layout = "@backend/views/layouts/main-base";

        $model = new ClearCache();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','清理成功');
        } 

        return $this->render($this->action->id, [
            'model' => $model
        ]);
    }

    public function actionSetCache()
    {
        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post('bloc','');
            if($data){
                Yii::$app->cache->set('globalBloc',json_decode($data,true));
            
                return ResultHelper::json(200,'切换成功',Yii::$app->cache->get('globalBloc')) ;
                    
            }else{
                
                return ResultHelper::json(200,'切换失败',[]) ;
                
            }
            
        }
    }
}
