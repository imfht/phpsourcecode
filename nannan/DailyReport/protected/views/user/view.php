<?php if(Yii::app()->user->name==='admin')
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'用户管理'=>array('admin'),
	$model->name,
);
?>
<?php
$this->widget('bootstrap.widgets.TbMenu',array(
	'type'=>'tabs',
	'items'=>array(
	array('label'=>'更改个人信息', 'url'=>array('update')),
	array('label'=>'管理用户信息','url'=>array('admin'),'visible'=>Yii::app()->user->name==='admin'),
	),
));
?>

<h1>您是第<?php echo $model->id; ?>个用户</h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'password',
		'email',
		array(
			'name'=>'教研室',
			'value'=>Room::roomname($model->roomid),
		),
		//'roomid',
		array(
			'name'=>'项目组',
			'value'=>Project::projectname($model->projectid),
		),
		//'projectid',
		array(
			'name'=>'是否接收日报邮件',
			'value'=>Bool::item($model->receive_email),
		),
		//'receive_email',
		array(
			'name'=>'是否接收邮件提醒',
			'value'=>Bool::item($model->receive_remind),
		),
		//'receive_remind',
	),
)); ?>
