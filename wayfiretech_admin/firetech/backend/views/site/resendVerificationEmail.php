<?php
/***
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:39
 * @LastEditTime: 2020-04-25 02:45:45
 */

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-22 18:12:04
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-22 18:12:04
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
<div class="site-resend-verification-email">
    <h1><?= Html::encode($this->title); ?></h1>

    <p>请填写您的电子邮件。一个重置密码的链接将被发送到那里</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'resend-verification-email-form']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]); ?>

            <div class="form-group">
                <?= Html::submitButton('Send', ['class' => 'btn btn-primary']); ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>