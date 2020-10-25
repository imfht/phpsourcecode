<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-04-30 16:53:04
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-04-30 16:53:14
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$settings = Yii::$app->settings;
$this->title = $settings->get('Website', 'name');
$intro = $settings->get('Website', 'intro');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="register-box ">
    <div class="register-logo">
        <a href="#" style="color:#ffffff"><b>
                <?= $this->title; ?>
            </b></a>
    </div>

    <div class="register-box-body box box-info ">
        <p class="login-box-msg">注册成为新用户</p>
        <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Full name']); ?>

        <?= $form->field($model, 'email')->textInput(['placeholder' => 'email']); ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'password']); ?>

        <div class="form-group">
            <?= Html::submitButton('注册', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'signup-button']); ?>
        </div>
        <?php ActiveForm::end(); ?>
        <a href="<?= Url::to(['/site/login']); ?>" class="text-center">已有账号去登录</a>
    </div>
</div>