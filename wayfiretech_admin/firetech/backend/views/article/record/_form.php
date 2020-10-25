<?php

use yii\helpers\Html;
use common\widgets\MyActiveForm;

/* @var $this yii\web\View */
/* @var $model common\addons\diandi_dingzuo\models\record */
/* @var $form yii\widgets\MyActiveForm */
?>

<div class="record-form">

    <?php $form = MyActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <?= $form->field($model, 'merchant')->textInput() ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php MyActiveForm::end(); ?>

</div>
