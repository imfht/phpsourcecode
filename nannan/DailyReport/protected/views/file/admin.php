<?php
/* @var $this FileController */
/* @var $model File */

$this->breadcrumbs=array(
	'Files'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List File', 'url'=>array('index')),
	array('label'=>'Create File', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#file-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Files</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'file-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		//'url',
		array(
			'name'=>'文件大小',
			'value'=>'round($data["size"],2)."MB"',
		),
		//'size',
		//'author_id',
		array(
			'name'=>'上传者',
			'value'=>'User::item($data["author_id"])',
		),
		//'info',
		'upload_time',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
