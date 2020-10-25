<?php
/* @var $this RoomController */
/* @var $model Room */

$this->breadcrumbs=array(
	'Rooms'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Room', 'url'=>array('index')),
	array('label'=>'Manage Room', 'url'=>array('admin')),
);
?>

<h1>Create Room</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>