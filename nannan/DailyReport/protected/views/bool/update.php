<?php
/* @var $this BoolController */
/* @var $model Bool */

$this->breadcrumbs=array(
	'Bools'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Bool', 'url'=>array('index')),
	array('label'=>'Create Bool', 'url'=>array('create')),
	array('label'=>'View Bool', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Bool', 'url'=>array('admin')),
);
?>

<h1>Update Bool <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>