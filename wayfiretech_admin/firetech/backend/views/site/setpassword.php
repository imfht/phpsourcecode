<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-10 01:06:50
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 01:08:11
 */
 
/***
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:39
 * @LastEditTime: 2020-04-25 02:46:00
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$settings = Yii::$app->settings;
$this->title = $settings->get('Website', 'name');
$intro = $settings->get('Website', 'intro');
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="login-box">
    <div class="login-logo">
        <a href="#" style="color:#ffffff"><b>修改密码</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body box box-info">
        <p class="login-box-msg"><?= $intro; ?></p>

        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

        <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]); ?>

        <div class="form-group text-right">
            <?= Html::submitButton('确认修改', ['class' => 'btn btn-primary']); ?>
        </div>

        <?php ActiveForm::end(); ?>


    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->