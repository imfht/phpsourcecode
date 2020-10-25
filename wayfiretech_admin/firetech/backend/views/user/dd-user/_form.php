<?php

use yii\helpers\Html;
use common\widgets\MyActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DdUser */
/* @var $form yii\widgets\MyActiveForm */
?>

<div class="dd-user-form">

    <?php $form = MyActiveForm::begin(); ?>

    <?= $form->field($model, 'open_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nickName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'avatarUrl')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gender')->textInput() ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'province')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_id')->textInput() ?>

    <?= $form->field($model, 'wxapp_id')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php MyActiveForm::end(); ?>

</div>
