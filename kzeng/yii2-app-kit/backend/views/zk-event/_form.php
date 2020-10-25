<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use trntv\yii\datetime\DateTimeWidget;


/* @var $this yii\web\View */
/* @var $model backend\models\ZkEvent */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="zk-event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'desc')->textarea(['maxlength' => true, 'rows' => 5]) ?>

    <!--
    <//?php echo $form->field($model, 'create_time')->textInput() ?>
    -->

    <?php echo $form->field($model, 'create_time')->widget(
        DateTimeWidget::className(),
        [
            'phpDatetimeFormat' => 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ',
            //'phpDatetimeFormat' => 'yyyy-MM-dd',
            //'phpDatetimeFormat' => 'dd.MM.yyyy, HH:mm',
        ]
    ) ?>


    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
