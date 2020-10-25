<?php
/* @var $this FileController */
/* @var $data File */
?>
<div class="view">
	<b>文件名：</b>
	<?php $this->widget('bootstrap.widgets.TbBadge',array(
		'type'=>($data->id)%2==0? 'success':'important',
		'label'=>$data->name,
		'htmlOptions'=>array('style'=>'font-size:135%'),
	));?>
	<br/>
	<b>上传者：</b>
	<?php $this->widget('bootstrap.widgets.TbBadge',array(
		'type'=>($data->id)%2==0? 'important':'success',
		'label'=>User::item($data->author_id),
		'htmlOptions'=>array('style'=>'font-size:135%'),
	));?>
	<br/>
	<b>文件信息：</b>
	<?php echo CHtml::encode($data->info); ?>
	<br/>
	<b>文件大小：</b>
	<?php echo CHtml::encode(round($data->size,2))."MB"; ?>
	<br/>
	<b>上传时间：</b>
	<?php echo CHtml::encode($data->upload_time); ?>
	<div class="row buttons" style="text-align:center">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'下载',
		'type'=>($data->id)%2==0? 'info':'success',
		'icon'=>'icon-download-alt',
        'url'=>array('download','id'=>$data->id),
        //'htmlOptions'=>array('style'=>'text-align:center'),
    )); ?>
	</div>
</div>