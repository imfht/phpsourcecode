<?php
/* @var $this DailyreportController */
/* @var $model Dailyreport */

$this->breadcrumbs=array(
	'日报'=>array('index'),
	'管理',
);

$this->menu=array(
	array('label'=>'日报列表', 'url'=>array('index')),
	array('label'=>'写日报', 'url'=>array('create')),
);
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dailyreport-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'author',
			'value'=>'User::item($data["author_id"])',
			'filter'=>User::items(),
		),
		'content',
		'create_time',
		//'id',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
