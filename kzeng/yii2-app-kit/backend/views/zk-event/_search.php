<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ZkEventSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="zk-event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'event_id') ?>

    <?php echo $form->field($model, 'title') ?>

    <?php echo $form->field($model, 'desc') ?>

    <?php echo $form->field($model, 'create_time') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
