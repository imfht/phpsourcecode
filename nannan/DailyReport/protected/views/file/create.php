<?php
/* @var $this FileController */
/* @var $model File */

$this->breadcrumbs=array(
	'Files'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List File', 'url'=>array('index')),
	array('label'=>'Manage File', 'url'=>array('admin')),
);
?>

<h1>Create File</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>