<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->context->layout = 'nologin';
$this->title = '登录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>请填写以下信息来登录:</p>

    <div class="row">
        <div class="col-lg-5">
			<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
			<?= $form->field($model, 'username') ?>
			<?= $form->field($model, 'password')->passwordInput() ?>
				<?= $form->field($model, 'rememberMe')->checkbox() ?>
			<div class="form-group">
			<?= Html::submitButton('登录', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
			</div>
<?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
