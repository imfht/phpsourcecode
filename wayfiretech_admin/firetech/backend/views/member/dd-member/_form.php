<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 11:53:36
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 11:54:05
 */
 

use yii\helpers\Html;
use common\widgets\MyActiveForm;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DdMember */
/* @var $form yii\widgets\MyActiveForm */
?>

<div class="dd-member-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'openid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nickName')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'avatarUrl')->widget('common\widgets\webuploader\FileInput', [])->label('商品主图'); ?>

    <?= $form->field($model, 'gender')->textInput() ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'province')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>




    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
