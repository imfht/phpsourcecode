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
<h2><strong>订单管理</strong></h2>
<h3><span class="title">编辑订单</span><span class="manage"><a href="javascript:void(0)"><img src="<?php echo Yii::app()->request->baseUrl;?>/system/images/delete.jpg" /></a></span></h3>

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
		'pageSize'=>3,		
	),
	'columns'=>array(
		array(
			'class'=>'CCheckBoxColumn',
			'name'=>'id',
		),
		
		array(
			'name'=>'id',
			'htmlOptions'=>array('style'=>'width:20px;'),
		),
		array(
			'name'=>'order_number',
			'header'=>'订单编号',
			'type'=>'raw',
			'value'=>'CHtml::link($data->order_number,array(update,id=>$data->id))',
		),
		

		
		array(
			'name'=>'mname',
			'header'=>'会员名',
		),
		
		array(
			'name'=>'gid',
			'header'=>'游戏',
			'value'=>'$data->gameName->gname',
			'filter'=>CHtml::listData(Games::model()->findAll(), 'id', 'gname'),
		),
		array(
			'name'=>'gid_server_id',
			'header'=>'分区',	
			'value'=>'Games::model()->getGamesServerValue($data->gid,$data->gid_server_id)',
		),
		array(
			'name'=>'price',
		),
		
		array(
			'name'=>'pay_type',
			'header'=>'支付方式',
			'filter'=>CHtml::listData(OrderType::model()->findAll(), 'id', 'name'),
			'value'=>'$data->orderType->name',
		),
		array(
			'name'=>'pay_time',
			'header'=>'支付时间',
			'value'=>'Order::model()->getTime($data->pay_time)',
		),
		array(
			'name'=>'pay',
			'header'=>'是否付款',
			'filter'=>Yii::app()->params["pay"],
			'value'=>'Order::model()->getPayName($data->pay)',
		),
		
		array(
			'header'=>'编辑',
			'class'=>'CButtonColumn',
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
<div class="search-form" style="display:none">
</div><!-- search-form -->
