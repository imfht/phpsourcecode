<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 11:53:40
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 11:53:40
 */
 

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\searchs\DdMemberSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dd-member-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'member_id') ?>

    <?= $form->field($model, 'openid') ?>

    <?= $form->field($model, 'nickName') ?>

    <?= $form->field($model, 'avatarUrl') ?>

    <?= $form->field($model, 'gender') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'province') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'address_id') ?>

    <?php // echo $form->field($model, 'wxapp_id') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'update_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
