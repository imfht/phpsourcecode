<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cron */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cron-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'task')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mhdmd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'job_script')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'param')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'intro')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'error_msg')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_ok')->checkbox() ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <?= $form->field($model, 'run_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
