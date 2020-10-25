<?php

use trntv\filekit\widget\Upload;
use trntv\yii\datetime\DateTimeWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use pendalf89\filemanager\widgets\TinyMCE;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categories common\models\ArticleCategory[] */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="article-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'slug')
        ->hint(Yii::t('backend', 'If you\'ll leave this field empty, slug will be generated automatically'))
        ->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'category_id')->dropDownList(\yii\helpers\ArrayHelper::map(
            $categories,
            'id',
            'title'
        ), ['prompt'=>'']) ?>


<!--
    <//?php echo $form->field($model, 'body')->widget(
        \yii\imperavi\Widget::className(),
        [
            'plugins' => ['fullscreen', 'fontcolor', 'video'],
            'options' => [
                'minHeight' => 400,
                'maxHeight' => 400,
                'buttonSource' => true,
                'convertDivs' => false,
                'removeEmptyTags' => false,
                'imageUpload' => Yii::$app->urlManager->createUrl(['/file-storage/upload-imperavi'])
            ]
        ]
    ) ?>
    -->



    <?= $form->field($model, 'body')->widget(TinyMCE::className(), [
        'clientOptions' => [
            'language' => 'zh_CN',
            'menubar' => false,
            //'menubar' => true,
            'height' => 500,
            'image_dimensions' => false,
            //'image_dimensions' => true,
            //'image_prepend_url' => 'http://127.0.0.1/yii2-app-kit/backend/web',
            'image_prepend_url' => Yii::getAlias('@backendUrl'),
            
            'plugins' => [
                'advlist autolink lists link image media emoticons template textcolor charmap print preview anchor searchreplace visualblocks code contextmenu table imagetools',
            ],
            'toolbar' => 'undo redo | styleselect |forecolor backcolor emoticons| bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media| code',
        ],
    ]); ?>

    <?php echo $form->field($model, 'thumbnail')->widget(
        Upload::className(),
        [
            'url' => ['/file-storage/upload'],
            'maxFileSize' => 5000000, // 5 MiB
        ]);
    ?>

    <?php echo $form->field($model, 'attachments')->widget(
        Upload::className(),
        [
            'url' => ['/file-storage/upload'],
            'sortable' => true,
            'maxFileSize' => 10000000, // 10 MiB
            'maxNumberOfFiles' => 10
        ]);
    ?>

    <?php echo $form->field($model, 'view')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'status')->checkbox() ?>

    <?php echo $form->field($model, 'published_at')->widget(
        DateTimeWidget::className(),
        [
            //'phpDatetimeFormat' => 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ',
            'phpDatetimeFormat' => 'yyyy-MM-dd',
            //'phpDatetimeFormat' => 'dd.MM.yyyy, HH:mm',
        ]
    ) ?>

    <div class="form-group">
        <?php echo Html::submitButton(
            $model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
