<style>
<!--
#side_right input{
	width:40px;
}
#side_right select{
	width:60px;
}
-->
</style>
<div id="side_right">
<h2><strong>游戏管理</strong></h2>
<h3><span class="title">编辑游戏</span><span class="manage"><a href="javascript:void(0)"><img src="<?php echo Yii::app()->request->baseUrl;?>/system/images/delete.jpg" /></a></span></h3>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'selectableRows'=>2,
	'filter'=>$model,
	'template'=>'{items}{pager}',
	'cssFile'=>false,
	'pager'=>array(
		'class'=>'CLinkPager',
		
		'cssFile'=>false,
		'header'=>'',
	),
	'columns'=>array(
		array('class'=>'CCheckBoxColumn','name'=>'id'),
		'id',
//		'gname',
		array(
			'name'=>'gname',
			'type'=>'raw',
			'value'=>'CHtml::link($data->gname,array(update,id=>$data->id))',
		),
		//'server_id',
		//'create_time',
		array(
			'name'=>'create_time',
			//'value'=>'Games::model()->getCreateTime($data->create_time)',
			'value'=>'Games::model()->getTime($data->create_time)',
		),
		array(
			'name'=>'display',
			'filter'=>Yii::app()->params['display'],
			'value'=>'Yii::app()->params[display][$data->display]',
		),
		array(
			'header'=>'编辑',
			'class'=>'CButtonColumn',
			'htmlOptions'=>array('style'=>'width:200px;'),
		),
	),
)); ?>

<?php $this->widget('CLinkPager',array(
	'pages'=>$pages,
	'header'=>'',
	'firstPageLabel'=>'首页',
	'lastPageLabel'=>'末页',
	'prevPageLabel'=>'上一页',
	'nextPageLabel'=>'下一页',
	'cssFile'=>false,
	//'htmlOptions'=>array('class'=>'yiiPager page'),
	'footer'=>''
));?>
</div>
<!---------------side_right end---------------->
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
