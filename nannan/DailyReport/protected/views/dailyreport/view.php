<?php
/* @var $this DailyreportController */
/* @var $model Dailyreport */

$this->widget('bootstrap.widgets.TbBreadcrumbs',array(
	'links'=>array('日报'=>array('index'),'今天我的日报'),
	'homeLink'=>false,
));?>

<h2>今天日报</h2>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'type'=>'bordered',
	'attributes'=>array(
		array(
			'name'=>'content',
			'label'=>'内容',
			'value'=>$model->content,
		),
		array(
			'name'=>'create_time',
			'label'=>'发布时间',	
			'value'=>$model->create_time,
		),
		array(
			'label'=>'作者',
			'value'=>Yii::app()->user->name,
		),
	),
)); ?>