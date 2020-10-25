<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


use pendalf89\filemanager\widgets\TinyMCE;
/* @var $this yii\web\View */
/* @var $model backend\models\Pform */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pform-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <!--    
        <//?= $form->field($model, 'uid')->textInput(['maxlength' => true]) ?>
    -->

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => '表单名称，如 活动报名']) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'placeholder' => '简短语句描述表单作用']) ?>

    <?= $form->field($model, 'file')->fileInput()->hint('1张表单页面头部图片，图片建议尺寸：900像素 * 300像素')  ?>
    <div>
        <?php
            if(empty($model->form_img_url))
            {
        ?>
            <img width="160" src="http://iph.href.lu/160x90?text=preview">
        <?php } else { ?>
            <img width="160" src="<?= Yii::$app->request->hostInfo . $model->form_img_url ?>">
        <?php } ?>
    </div>
<!--     
    <//?= $form->field($model, 'created_at')->textInput() ?>

    <//?= $form->field($model, 'updated_at')->textInput() ?> 
-->

<!--
    <//?= $form->field($model, 'user_id')->textInput() ?>
-->

    <?= $form->field($model, 'detail')->widget(TinyMCE::className(), [
        'clientOptions' => [
            'language' => 'zh_CN',
            'menubar' => false,
            //'menubar' => true,
            'height' => 360,
            'image_dimensions' => false,
            //'image_dimensions' => true,
            //'image_prepend_url' => 'http://127.0.0.1/yii2-app-kit/backend/web',
            //'image_prepend_url' => Yii::getAlias('@backend'),
            //'image_prepend_url' => 'http://pf.mitoto.cn/admin',

            'plugins' => [
                'advlist autolink lists link image media template textcolor colorpicker charmap print preview anchor searchreplace visualblocks code contextmenu table imagetools',
            ],
            'toolbar' => 'undo redo | styleselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media| code',
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
