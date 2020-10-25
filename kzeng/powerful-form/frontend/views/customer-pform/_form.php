<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\CustomerPform */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-pform-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pform_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pform_field_id')->textInput() ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
