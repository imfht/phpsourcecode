<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 11:42:30
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 11:43:14
 */
 

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DdMemberGroup */
/* @var $form yii\widgets\MyActiveForm */
?>

<div class="dd-member-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'item_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'level')->dropDownList([
        1=>1,
        2=>2,
        3=>3,
        4=>4,
        5=>5,
    ]) ?>

    <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
      
    </div>

    <?php ActiveForm::end(); ?>

</div>
