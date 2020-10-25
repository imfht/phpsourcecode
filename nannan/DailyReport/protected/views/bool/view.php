<?php
/* @var $this BoolController */
/* @var $model Bool */

$this->breadcrumbs=array(
	'Bools'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Bool', 'url'=>array('index')),
	array('label'=>'Create Bool', 'url'=>array('create')),
	array('label'=>'Update Bool', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Bool', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Bool', 'url'=>array('admin')),
);
?>

<h1>View Bool #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'is_true',
	),
)); ?>
