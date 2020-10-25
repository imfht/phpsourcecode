<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\WxOffice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wx-office-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'gh_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'scene_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'branch')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'region')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'manager')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'member_cnt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pswd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lat')->textInput() ?>

    <?= $form->field($model, 'lon')->textInput() ?>

    <?= $form->field($model, 'lat_bd09')->textInput() ?>

    <?= $form->field($model, 'lon_bd09')->textInput() ?>

    <?= $form->field($model, 'visable')->textInput() ?>

    <?= $form->field($model, 'is_jingxiaoshang')->textInput() ?>

    <?= $form->field($model, 'role')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'is_selfOperated')->textInput() ?>

    <?= $form->field($model, 'score')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
