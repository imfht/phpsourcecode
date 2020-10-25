<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<label>是否接收日报邮件：</label>
		<?php echo $form->dropDownList($model,'receive_email',Bool::items()); ?>
		<?php echo $form->error($model,'receive_email'); ?>
	</div>
	<div class="row">
		<label>是否接收日报邮件提醒：</label>
		<?php echo $form->dropDownList($model,'receive_remind',Bool::items()); ?>
		<?php echo $form->error($model,'receive_remind'); ?>
	</div>
	<div class="row buttons" style="text-align:center">
		<?php $this->widget('bootstrap.widgets.TbButton',array(
			'buttonType'=>'submit',
			'type'=>'success',
			'label'=>$model->isNewRecord? '提交':'保存',
			'size'=>'large',
			'icon'=>'icon-ok',
		));?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->