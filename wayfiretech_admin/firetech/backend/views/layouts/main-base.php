<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-14 23:50:50
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-01 11:02:02
 */


use richardfan\widget\JSRegister;
use yii\helpers\Html;
use yii\web\View;

$is_addons = Yii::$app->params['is_addons'];
/* @var $this \yii\web\View */
/* @var $content string */
$this->registerJs("window.sysinfo={
    'CSRF_HEADER':'". \yii\web\Request::CSRF_HEADER ."',".
    "csrfToken:'". Yii::$app->request->csrfToken."'};",View::POS_HEAD);

if (Yii::$app->controller->action->id === 'login' || Yii::$app->controller->action->id === 'signup') {
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

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@common/widgets/adminlte/asset'); ?>
  
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

    <body class="hold-transition <?= Yii::$app->params['Website']['themcolor'] ?>   sidebar-mini fixed">

        <?php $this->beginBody() ?>
        <div class="wrapper">
            <?= $this->render(
                'header.php',
                ['directoryAsset' => $directoryAsset]
            ) ?>
            <?= $this->render(
                'left.php',
                ['directoryAsset' => $directoryAsset]
            )
            ?>
            <?= $this->render(
                'content-base.php',
                ['content' => $content, 'directoryAsset' => $directoryAsset]
            ) ?>



            <?= $this->render(
                'footer.php',
                [
                    'content' => $content, 
                    'directoryAsset' => $directoryAsset,
                    'is_addons'=>$is_addons
                ]
            ) ?>

        </div>



        <?php $this->endBody() ?>
    </body>

    </html>
    <?php $this->endPage() ?>
<?php
} ?>