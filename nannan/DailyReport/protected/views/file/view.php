<?php
/* @var $this FileController */
/* @var $model File */

$this->breadcrumbs=array(
	'Files'=>array('index'),
	$model->name,
);
?>
<h1>View File #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'url',
		'size',
		'type',
		'author_id',
		'info',
		'upload_time',
	),
)); ?>
