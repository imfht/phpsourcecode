<?php

use yii\helpers\Html;
use common\widgets\MyActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\联系我们 */
/* @var $form yii\widgets\MyActiveForm */
?>

<div class="dd-website-contact-form">

    <?php $form = MyActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'createtime')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updatetime')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php MyActiveForm::end(); ?>

</div>
