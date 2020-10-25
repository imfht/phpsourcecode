<?php
/* @var $this DailyreportController */
/* @var $model Dailyreport */
/* @var $form CActiveForm */
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dailyreport-form',
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>true,
)); ?>
	
	<div class="row">
		<?php echo $form->textArea($model,'content',array('rows'=>8,'cols'=>20,'onkeyup'=>'read()','style'=>"height:150px;width:450px;")); ?>
		<div><span id="num" style="color:purple">已输入0个字符</span></div>
		<?php echo $form->error($model,'content'); ?>
	</div>
	<div class="row buttons" style="text-align:center">
	<?php $this->widget('bootstrap.widgets.TbButton',array(
		'buttonType'=>'submit',
		'type'=>$model->isNewRecord? 'success':'info',
		'size'=>'large',
		'icon'=>'icon-ok',
		'label'=>$model->isNewRecord? '发布':'保存',
	));?>
	</div>
<?php $this->endWidget(); ?>

</div>