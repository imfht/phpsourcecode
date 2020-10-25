<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PformFieldSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pform-field-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'placeholder') ?>

    <?php // echo $form->field($model, 'sort') ?>

    <?php // echo $form->field($model, 'pform_uid') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
