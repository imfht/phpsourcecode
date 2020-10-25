<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\WxOfficeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wx-office-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'office_id') ?>

    <?= $form->field($model, 'gh_id') ?>

    <?= $form->field($model, 'scene_id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'branch') ?>

    <?php // echo $form->field($model, 'region') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'manager') ?>

    <?php // echo $form->field($model, 'member_cnt') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'pswd') ?>

    <?php // echo $form->field($model, 'lat') ?>

    <?php // echo $form->field($model, 'lon') ?>

    <?php // echo $form->field($model, 'lat_bd09') ?>

    <?php // echo $form->field($model, 'lon_bd09') ?>

    <?php // echo $form->field($model, 'visable') ?>

    <?php // echo $form->field($model, 'is_jingxiaoshang') ?>

    <?php // echo $form->field($model, 'role') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'is_selfOperated') ?>

    <?php // echo $form->field($model, 'score') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
