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
<h2><strong>文章管理</strong></h2>
<h3><span class="title">管理文章</span><span class="manage"><a href="javascript:void(0)"><img src="<?php echo Yii::app()->request->baseUrl;?>/system/images/delete.jpg" /></a></span></h3>

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
			'name'=>'tilte',
			'type'=>'raw',
			'value'=>'CHtml::link($data->tilte,array(update,id=>$data->id))',
		),
		array(
			'name'=>'gid',
			'filter'=>CHtml::listData(Games::model()->findAll(), 'id', 'gname'),
			'value'=>'$data->gameName->gname',
		),
		array(
			'name'=>'tid',
			'filter'=>CHtml::listData(ArticleType::model()->findAll(), 'id', 'typename'),
			'value'=>'$data->articleType->typename',
		),
		
		array('name'=>'create_time','value'=>'date("Y-m-d",$data->create_time)'),
		array('name'=>'up_time','value'=>'date("Y-m-d",$data->up_time)'),
		array(
			'name'=>'display',
			'filter'=>Yii::app()->params['display'],
			'value'=>'Article::model()->getArticleDisplay($data->display)',
		),
		
		array(
			'header'=>'编辑',
			'class'=>'CButtonColumn',
			'htmlOptions'=>array(),
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
</div><!-- search-form -->

