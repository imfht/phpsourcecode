<div class="span10">
<?php if(!Yii::app()->user->isGuest)
$this->widget('bootstrap.widgets.TbMenu',array(
	'type'=>'tabs',
	'items'=>array(
	array('label'=>'写日报','icon'=>'pencil','url'=>array('create')),
	array('label'=>'今天日报','icon'=>'eye-open','url'=>array('view')),
	array('label'=>'更改日报','icon'=>'edit','url'=>array('update')),
	array('label'=>'日报记录','icon'=>'list-alt','url'=>array('show')),
	array('label'=>'请假了','icon'=>'icon-plane','url'=>array('/user/off')),
	array('label'=>'日报设置','icon'=>'cog','url'=>array('/user/modify')),
	array('label'=>'添加项目','url'=>array('/project/create'),'visible'=>Yii::app()->user->name==='admin'),
	array('label'=>'添加房间','url'=>array('/room/create'),'visible'=>Yii::app()->user->name==='admin'),
	array('label'=>'开启定时任务','url'=>array('open'),'visible'=>Yii::app()->user->name==='admin'),
	array('label'=>'邮件提醒','url'=>array('remind'),'visible'=>Yii::app()->user->name==='admin'),
	array('label'=>'管理日报', 'url'=>array('admin'),'visible'=>Yii::app()->user->name==='admin'),
	),
));
?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'dailyreport-grid',
	'type'=>'bordered',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'作者',
			'value'=>'User::item($data["author_id"])',
			'filter'=>User::items(),
			'htmlOptions'=>array('style'=>'width:55px;text-align:center'),
		),
		array(
			'name'=>'日报内容',
			'value'=>'$data["content"]',
			'filter'=>false,
			'htmlOptions'=>array('style'=>'width:500px'),
		),
		//'content',
		//'create_time',
		array(
			'name'=>'项目组',
			'value'=>'Project::item($data["author_id"])',
			'filter'=>false,
			'htmlOptions'=>array('style'=>'width:160px;text-align:center'),
		),
		array(
			'name'=>'教研室',
			'value'=>'Room::item($data["author_id"])',
			'filter'=>false,
			'htmlOptions'=>array('style'=>'width:50px;text-align:center'),
		),
		array(
			'name'=>'发布时间',
			// 'value'=>'Yii::app()->format->formatDateTime($data["create_time"])',
			'value'=>'$data["create_time"]',
			'filter'=>false,
			'htmlOptions'=>array('style'=>'width:130px;text-align:center'),
		),
	),
)); ?>
</div>

<meta charset='utf-8'>