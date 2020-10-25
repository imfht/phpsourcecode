<div id="main_right">
<div class="email">
    <h2>修改资料</h2>
    <?php $form = $this->beginWidget('CActiveForm',array(
        'id'=>'member-form',
    	'enableAjaxValidation'=>true,
    	'enableClientValidation'=>true,
    	'clientOptions'=>array(
    		'validateOnSubmit'=>true,
    	),
    )); ?>
		<div class="row">
			<span class="title">
				<?php echo $form->labelEx($model,'qq'); ?>
			</span>
			<?php echo $form->textField($model,'qq',array('value'=>'','size'=>19)); ?>
			<?php echo $form->error($model,'qq'); ?>
		</div>
	
		<div class="row">
			<span class="title">
				<?php echo $form->labelEx($model,'telephone'); ?>
			</span>
			<?php echo $form->textField($model,'telephone',array('size'=>19,'maxlength'=>150,'value'=>'')); ?>
			<?php echo $form->error($model,'telephone'); ?>
		</div>
		
		<div class="row">
			<span class="title">
				<?php echo $form->labelEx($model,'address'); ?>
			</span>
			<?php echo $form->textField($model,'address',array('size'=>19,'maxlength'=>100,'value'=>'')); ?>
			<?php echo $form->error($model,'address'); ?>
		</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('确认修改'); ?>
    <?php $this->endWidget(); ?>
</div> 
</div>
</div>