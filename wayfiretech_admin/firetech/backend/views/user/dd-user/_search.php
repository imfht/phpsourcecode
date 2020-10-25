<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DdUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dd-user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'open_id') ?>

    <?= $form->field($model, 'nickName') ?>

    <?= $form->field($model, 'avatarUrl') ?>

    <?= $form->field($model, 'gender') ?>

    <?php // echo $form->field($model, 'country')?>

    <?php // echo $form->field($model, 'province')?>

    <?php // echo $form->field($model, 'city')?>

    <?php // echo $form->field($model, 'address_id')?>

    <?php // echo $form->field($model, 'wxapp_id')?>

    <?php // echo $form->field($model, 'create_time')?>

    <?php // echo $form->field($model, 'update_time')?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
