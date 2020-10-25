<div id="main_right">
<div class="email">
    <h2>防沉迷验证</h2>
    <?php if ($model->id_card != ""): ?>
		<p>您的身份已认证成功！</p>
		<p style="color:#999999;">真实姓名：<?php echo $model->real_name;?></p>
		<p style="color:#999999;">身份证号：<?php echo substr_replace($model->id_card,'xxxxxxxxx',3,12);?></p>
    <?php endif; ?>
   
    <?php if ($model->id_card == ""): ?>
     <p>填写您的信息</p>	
    	<?php $form=$this->beginWidget('CActiveForm',array(
    		'id'=>'member-form',
    		'enableAjaxValidation'=>true,	
    		'enableClientValidation'=>true,
    		'clientOptions'=>array('validateOnSubmit'=>true),
    	));?>
    	<div>
    		<?php echo $form->labelEx($model,'real_name');?> 
    		<?php echo $form->textField($model,'real_name');?>
    		<?php echo $form->error($model,'real_name');?>
    	</div>
    	<div style="margin-top:15px;">
    		<?php echo $form->labelEx($model,'id_card');?> 
    		<?php echo $form->textField($model,'id_card');?>
			<?php echo $form->error($model,'id_card');?>
    	</div>
    	<div class="buttons">
    		<p style="padding-left:60px;"><?php echo CHtml::submitButton('确认绑定');?></p>
    	</div>
    	<?php $this->endWidget();?>
    <?php endif;?>
    
</div>
</div>