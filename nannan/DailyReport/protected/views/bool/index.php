<?php
/* @var $this BoolController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Bools',
);

$this->menu=array(
	array('label'=>'Create Bool', 'url'=>array('create')),
	array('label'=>'Manage Bool', 'url'=>array('admin')),
);
?>

<h1>Bools</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
