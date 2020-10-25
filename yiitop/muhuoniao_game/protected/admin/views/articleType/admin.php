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
<h2><strong>栏目管理</strong></h2>
<h3><span class="title">编辑栏目</span><span class="manage"><a href="javascript:void(0)"><img src="<?php echo Yii::app()->request->baseUrl;?>/system/images/delete.jpg" /></a></span></h3>

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
			'name'=>'tid',
			'value'=>'ArticleType::model()->getParentArticleType($data->tid)',
		),
		//'typename',
		array(
			'name'=>'typename',
			'type'=>'raw',
			'value'=>'CHtml::link($data->typename,array(update,id=>$data->id))',
		),
		//'create_author_id',
		//'up_author_id',
		'create_time',
		'up_time',
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


