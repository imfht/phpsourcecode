<?php

use yii\helpers\Html;
use common\widgets\MyActiveForm;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model common\models\DdArticleCategory */
/* @var $form yii\widgets\MyActiveForm */
?>

<div class="dd-article-category-form">

    <?php $form = MyActiveForm::begin(); ?>
    <?=  $form->field($model, 'pcate')->dropDownList(ArrayHelper::map($catedata, 'id', 'title'), [
                    // 'options' => ['5' => ['selected' => true]],
                    'prompt' => ['text' => '顶级分类', 'options' => ['value' => 0]],
                    
                ]);?>

    
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'displayorder')->textInput() ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php MyActiveForm::end(); ?>

</div>
