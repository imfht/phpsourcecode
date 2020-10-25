<?php if(Yii::app()->user->hasFlash('success')):?>
<?php $this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('培训信息'=>array('index'),'上传资料'=>array('upload'),'结果'),
	'homeLink'=>false,
));?>
<div id="flash-success">
	<?php $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
)); ?>
</div>

<?php elseif(Yii::app()->user->hasFlash('info')):?>
<?php $this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('培训信息'=>array('index'),'上传资料'),
	'homeLink'=>false,
));?>
<div id="flash-success">
	<?php $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'info'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
)); ?>
</div>

<div class="form">
<?php
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'file-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data')
));?>
<div class="row">
<span style="white-space:pre">    </span><?php echo CHtml::activeFileField($model,'url');?>
<span style="white-space:pre">    </span><?php echo $form->error($model,'url');?>
</div>
<div class="row">
		<?php echo '<br/><p style="color:red;font-size:130%">请简单介绍文件相关信息:</p>'; ?>
		<?php echo $form->textArea($model,'info',array('rows'=>6, 'cols'=>60,'style'=>"height:140px;width:300px")); ?>
		<?php echo $form->error($model,'info'); ?>
</div>
<div class="row buttons" style="text-align:center">
	<?php $this->widget('bootstrap.widgets.TbButton',array(
		'buttonType'=>'submit',
		'type'=>'success',
		'size'=>'large',
		'icon'=>'icon-upload',
		'label'=>'上传文件'
	));?>
</div>
<?php $this->endWidget();?>
</div>
<?php endif; ?>