<?php
/* @var $this BoolController */
/* @var $model Bool */

$this->breadcrumbs=array(
	'Bools'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Bool', 'url'=>array('index')),
	array('label'=>'Manage Bool', 'url'=>array('admin')),
);
?>

<h1>Create Bool</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>