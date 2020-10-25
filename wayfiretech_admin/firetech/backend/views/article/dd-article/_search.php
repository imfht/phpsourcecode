<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\searchs\DdArticleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dd-article-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ishot') ?>

    <?= $form->field($model, 'pcate') ?>

    <?= $form->field($model, 'ccate') ?>

    <?= $form->field($model, 'template') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'content') ?>

    <?php // echo $form->field($model, 'thumb') ?>

    <?php // echo $form->field($model, 'incontent') ?>

    <?php // echo $form->field($model, 'source') ?>

    <?php // echo $form->field($model, 'author') ?>

    <?php // echo $form->field($model, 'displayorder') ?>

    <?php // echo $form->field($model, 'linkurl') ?>

    <?php // echo $form->field($model, 'createtime') ?>

    <?php // echo $form->field($model, 'edittime') ?>

    <?php // echo $form->field($model, 'click') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'credit') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
