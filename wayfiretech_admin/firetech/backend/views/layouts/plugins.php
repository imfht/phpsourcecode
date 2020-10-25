<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-14 23:52:27
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 20:25:13
 */


use richardfan\widget\JSRegister;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */


if (Yii::$app->controller->action->id === 'login') {
    /**
     * Do not use this code in your template. Remove it.
     * Instead, use the code  $this->layout = '//main-login'; in your controllers.
     */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {
    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    }

    common\widgets\adminlte\AdminLteAsset::register($this);

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/yii-diandi/adminlte/asset/dist'); ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">

    <head>
        <meta charset="<?= Yii::$app->charset ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
       
    </head>

    <body class="hold-transition skin-blue sidebar-mini fixed">

        <?php $this->beginBody() ?>
        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

        <?php $this->endBody() ?>
    </body>

    </html>
    <?php $this->endPage() ?>
<?php
} ?>