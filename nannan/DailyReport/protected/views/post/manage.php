<?php
/* @var $this PostController */
/* @var $model Post */

$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('重要信息'=>array('index'),'查看信息'),
	'homeLink'=>false,
));
?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'type'=>'bordered',
	'id'=>'post-grid',
	'dataProvider'=>$model->searchMyInfo(),
	//'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'编号',
			'value'=>'$data["id"]',
		),
		//'id',
		array(
			'name'=>'内容',
			'value'=>'$data["content"]',
		),
		array(
			'name'=>'作者',
			'value'=>'$data["author"]->name',
		),
		array(
			'name'=>'发布时间',
			'value'=>'$data["post_time"]',
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>