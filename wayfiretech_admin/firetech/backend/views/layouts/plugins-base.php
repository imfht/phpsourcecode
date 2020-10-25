<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-14 23:52:39
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 20:25:06
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
        <style>
            #tab-content {
                padding: 15px;
                min-height: 100vh;

            }
        </style>
    </head>
    <!-- <?= \common\widgets\adminlte\AdminLteHelper::skinClass() ?> -->

    <body class="sidebar-mini fixed skin-purple">

        <?php $this->beginBody() ?>
        <?= $this->render(
            'left-plugins.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10" id="content-wrapper" style="height: auto;padding-top:20px;">

            <div class="content-iframe " style="background-color: #ffffff; ">
                <section class="content-header" style="padding: 15px;">
                    <a href="javascript:window.location.reload()" class="rfHeaderFont">
                        <i class="glyphicon glyphicon-refresh"></i> 刷新
                    </a>
                    <a href="javascript:history.go(-1)" class="backMenu" style="display: none;">
                        <i class="fa fa-mail-reply"></i> 返回
                    </a>
                    <ol class="breadcrumb">
                        <li><a href="/backend/"><i class="fa fa-dashboard"></i><?= $this->title ?></a></li>
                        <li class="active">首页</li>
                    </ol>
                </section>
                <div class="tab-content " id="tab-content">

                    <?= $content ?>
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->

        <?php $this->endBody() ?>
    </body>

    </html>
    <?php $this->endPage() ?>
<?php
} ?>