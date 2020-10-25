<?php
/* @var $this PostController */
/* @var $model Post */

$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('重要信息'=>array('index'),'管理信息'=>array('manage'),'查看信息'),
	'homeLink'=>false,
));
?>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'name'=>'内容',
			'value'=>$model->content,
		),
		array(
			'name'=>'作者',
			'value'=>$model->author->name,
		),
		array(
			'name'=>'时间',
			'value'=>$model->post_time,
		),
	),
)); ?>
