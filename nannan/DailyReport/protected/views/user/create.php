<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'主页'=>Yii::app()->homeUrl,
	'用户'=>array('index'),
	'注册',
);
?>
<?php if(Yii::app()->user->name==='admin')
	$this->menu=array(
		array('label'=>'用户列表', 'url'=>array('index')),
		array('label'=>'管理用户', 'url'=>array('admin')),
);
?>
<h2>注册用户</h2>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>