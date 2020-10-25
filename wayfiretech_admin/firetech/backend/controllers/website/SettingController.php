<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-28 23:43:29
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-04-30 23:45:40
 */

namespace backend\controllers\website;

use  backend\controllers\BaseController;

/**
 * Class SiteController.
 */
class SettingController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'website' => [
                'class' => \yii2mod\settings\actions\SettingsAction::class,
                'view' => 'website',
                'successMessage' => '保存成功',
                // also you can use events as follows:
                'on beforeSave' => function ($event) {
                    // your custom code
                },
                'on afterSave' => function ($event) {
                    // your custom code
                },
                'modelClass' => \common\models\forms\Website::class,
            ],
        ];
    }
}
