<?php
$this->widget('bootstrap.widgets.TbMenu',array(
	'type'=>'tabs',
	'stacked'=>false,
	'items'=>array(
		array('label'=>'用户列表', 'url'=>array('index')),
	),
));
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'password',
		'email',
		'roomid',
		'projectid',
		/*
		'receive_email',
		'receive_remind',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
