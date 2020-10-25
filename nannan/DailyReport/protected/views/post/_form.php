<?php
/* @var $this PostController */
/* @var $model Post */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'post-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'title');?>
		<?php echo $form->textField($model,'title');?>
		<?php echo $form->error($model,'title');?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50,'style'=>"height:220px;width:400px")); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row buttons" style="text-align:center">
		<?php $this->widget('bootstrap.widgets.TbButton',array(
			'buttonType'=>'submit',
			'type'=>$model->isNewRecord? 'info':'success',
			'size'=>'large',
			'label'=>$model->isNewRecord? '发布':'保存',
			'icon'=>'icon-ok',
		));?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->