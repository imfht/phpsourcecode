<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */
	$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
		'links'=>array('用户管理'=>array('admin'),'用户列表'),
		'homeLink'=>false,
	));
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
