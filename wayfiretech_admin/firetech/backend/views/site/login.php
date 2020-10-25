<?php
/*** 
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:38
 * @LastEditTime: 2020-04-26 09:39:58
 */
/***
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:38
 * @LastEditTime: 2020-04-25 02:47:26
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$settings = Yii::$app->settings;
$this->title = $settings->get('Website', 'name');
$intro = $settings->get('Website', 'intro');

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>",
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>",
];
?>

<div class="login-box">
    <div class="login-logo">
        <a href="#" style="color:#ffffff"><b><?= $this->title; ?></b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body box box-info">
        <p class="login-box-msg"><?= $intro; ?></p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]); ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]); ?>

        <div class="row">
            <div class="col-xs-4">
                <?= $form->field($model, 'rememberMe')->checkbox(); ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-8">

                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <?= Html::a(
                        '注册',
                        ['/site/signup'],
                        ['class' => 'btn btn-primary btn-block btn-flat']
                    ); ?>

                </div>

                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right:0px;">
                    <?= Html::submitButton('登录', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']); ?>

                </div>


            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>


        <!-- /.social-auth-links -->

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-right">
                <a href="<?= Url::to(['/site/request-password-reset']); ?>" class="pull-right">忘记密码</a>
            </div>

        </div>


    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->