<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use vova07\imperavi\Widget;
/* @var $this yii\web\View */
/* @var $model common\models\Page */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="page-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

   <?php echo $form->field($model, 'body')->widget(Widget::className(), [
        'settings' => [
            'lang' => 'zh_cn',
            'minHeight'=>200,
            'maxHeight'=>400,
            'buttonSource'=>true,
            'convertDivs'=>false,
            'removeEmptyTags'=>false,
            'plugins' => [
                'clips',
                'fullscreen',
                'fontcolor',
                'fontfamily',
                'fontsize',
                'limiter',
                'table',
                'textexpander',
                'textdirection',
                'video',
                'definedlinks',
                'filemanager',
                'imagemanager',
            ],
                // 'imageManagerJson' => Url::to(['/goods/imagesget']),
                //'imageUpload' => Url::to(['/goods/imageupload']),
                //'fileUpload' => Url::to(['/goods/fileupload']),
                'imageUpload'=>Yii::$app->urlManager->createUrl(['/file-storage/upload-imperavi'])
        ]
    ]); ?>

    <?php echo $form->field($model, 'view')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
