<?php
/* @var $this BoolController */
/* @var $data Bool */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_true')); ?>:</b>
	<?php echo CHtml::encode($data->is_true); ?>
	<br />


</div>