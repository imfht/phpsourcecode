<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-22 14:08:47
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-04 19:26:01
 */
use yii\helpers\Html;
use common\widgets\MyActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DdArticle */
/* @var $form yii\widgets\MyActiveForm */
?>

<div class="dd-article-form">

    <?php $form = MyActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'ishot')->radioList(['1' => '热点', '0' => '非热点']); ?>

    <?= $form->field($model, 'pcate')->dropDownList($Helper->courseCateMap(), [
                    'prompt' => ['text' => '一级分类', 'options' => ['value' => 0]],
                    'label' => '一级分类',
                    'id' => 'classsearch-cocate_id',
                ])->label('一级分类'); ?>

    <?= $form->field($model, 'ccate')->dropDownList($Helper->courseMap($model->ccate), [
                    'prompt' => ['text' => '二级分类', 'options' => ['value' => 0]],
                    'id' => 'classsearch-course_id',
                ])->label('二级分类 '); ?>

    <?= $form->field($model, 'template')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]); ?>
    <?= $form->field($model, 'icon')->widget('common\widgets\adminlte\Icon', ['options' => [
                'label' => '选择图标',
            ]]); ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]); ?>

      <?= $form->field($model, 'content')->widget('common\widgets\ueditor\UEditor', [
            'clientOptions' => [
            //编辑区域大小
            'initialFrameHeight' => '200',
            //设置语言
            'lang' => 'en', //中文为 zh-cn
            ],
            'options' => [
                'id' => 'ddgoods-content',
            ],
        ]); ?>
    <?= $form->field($model, 'thumb')->widget('common\widgets\webuploader\FileInput', [])->label('商品主图'); ?>

    <?= $form->field($model, 'source')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'displayorder')->textInput(); ?>

    <?= $form->field($model, 'linkurl')->textInput(['maxlength' => true]); ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']); ?>
        </div>
    </div>

    <?php MyActiveForm::end(); ?>

</div>
