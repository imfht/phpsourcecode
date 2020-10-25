<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'items'=>array(
        array('label'=>'上传资料', 'icon'=>'icon-upload','url'=>array('upload')),
		array('label'=>'管理资料','icon'=>'icon-file','url'=>array('admin'),'visible'=>Yii::app()->user->name=='admin'),
    ),
)); ?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
