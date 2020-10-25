<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>true,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<div class="row">
		<label>用户名（请填写真实姓名）<span class="required">*</span></label>
		<?php echo $form->textField($model,'name',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<label>教研室：</label>
		<?php echo $form->dropDownList($model,'roomid',Room::items());?>
		<?php echo $form->error($model,'roomid'); ?>
	</div>
	<div class="row">
		<label>项目组：</label>
		<?php echo $form->dropDownList($model,'projectid',Project::items()); ?>
		<?php echo $form->error($model,'projectid'); ?>
	</div>
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