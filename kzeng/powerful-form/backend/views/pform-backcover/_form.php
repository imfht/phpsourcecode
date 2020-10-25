<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PformBackcover */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="pform-backcover-form">

    <?php $form = ActiveForm::begin(); ?>

 	<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'content')->widget(\yii\redactor\widgets\Redactor::className(), [
        'clientOptions' => [
            'minHeight'=>200,
            'maxHeight'=>400,
            'imageManagerJson' => ['/redactor/upload/image-json'],
            'imageUpload' => ['/redactor/upload/image'],
            'fileUpload' => ['/redactor/upload/file'],
            'lang' => 'zh_cn',
            'plugins' => ['clips', 'fontcolor','imagemanager']
        ]
    ])?>

	<input type="hidden" id="pformbackcover-pform_uid" class="form-control" name="PformBackcover[pform_uid]" maxlength="64">
	<!--
    <//?= $form->field($model, 'pform_uid')->textInput(['maxlength' => true]) ?>
    -->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php 
	if( $flag == "create") 
	{
?>
	<script type="text/javascript">
		$("#pformbackcover-pform_uid").val("<?= $uid ?>");
	</script>
<?php } ?>