<?php
/*** 
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:38
 * @LastEditTime: 2020-04-25 02:45:34
 */


/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$settings = Yii::$app->settings;
$this->title = $settings->get('Website', 'name');
$intro = $settings->get('Website', 'intro');
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="login-box">
    <div class="login-logo">
        <a href="#" style="color:#ffffff"><b><?= $this->title; ?></b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body box box-info">
        <p class="login-box-msg"><?= $intro; ?></p>
        <p>请填写您的电子邮件。一个重置密码的链接将被发送到那里</p>

        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]); ?>

        <div class="row">
            <!-- /.col -->
            <div class="col-xs-8" style="line-height: 35px;">
                <a href="<?= Url::to(['/site/signup']); ?>" class="text-center">注册/</a>
                <a href="<?= Url::to(['/site/login']); ?>" class="text-center">登录</a>
            </div>

            <div class="col-xs-4">
                <?= Html::submitButton('发送', ['class' => 'btn btn-primary btn-block']); ?>

            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>


    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->