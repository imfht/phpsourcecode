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
<h2><strong>游戏API管理</strong></h2>
<h3><span class="title">编辑API</span><span class="manage"><a href="javascript:void(0)"><img src="<?php echo Yii::app()->request->baseUrl;?>/system/images/delete.jpg" /></a></span></h3>

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
		array(
			'name'=>'gid',
			'type'=>'raw',
			'filter'=>Games::model()->getGamesAll(),
			'value'=>'CHtml::link($data->gameName->gname,array(update,id=>$data->id))',
		),
		'userid',
		'username',
		'password',
		array(
			'header'=>'编辑',
			'class'=>'CButtonColumn',
			'htmlOptions'=>array('style'=>'width:200px;'),
		),
	),
)); ?>
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
