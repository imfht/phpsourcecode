<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-25 17:40:09
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-25 17:43:51
 */
 

use yii\helpers\Html;
use common\widgets\MyActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DdWebsiteSlide */
/* @var $form yii\widgets\MyActiveForm */
?>

<div class="dd-website-slide-form">

    <?php $form = MyActiveForm::begin(); ?>

    <?= $form->field($model, 'images')->widget('common\widgets\webuploader\FileInput', []);?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'menuname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'menuurl')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php MyActiveForm::end(); ?>

</div>
