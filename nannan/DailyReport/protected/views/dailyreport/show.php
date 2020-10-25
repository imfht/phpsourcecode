<?php
/* @var $this DailyreportController */
/* @var $dataProvider CActiveDataProvider */

$this->widget('bootstrap.widgets.TbBreadCrumbs',array(
	'links'=>array('日报'=>array('index'),'自己日报记录'),
	'homeLink'=>false,
));
?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'dailyreport-grid',
	'dataProvider'=>$model->searchMyinfo(),
	//'dataProvider'=>Yii::app()->user->dailyreports,
	//'filter'=>$model,
	'columns'=>array(
		// array(
			// 'name'=>'作者',
			// 'value'=>'User::item($data["author_id"])',
			// 'filter'=>false,
		// ),
		array(
		'name'=>'日报内容',
		'value'=>'$data["content"]',
		'filter'=>false,
		),
		array(
		'name'=>'时间',
		'value'=>'$data["create_time"]',
		'filter'=>false,
		),
	),
)); ?>