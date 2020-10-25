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
<h2><strong>会员管理</strong></h2>
<h3><span class="title">编辑会员</span><span class="manage"><a href="javascript:void(0)"><img src="<?php echo Yii::app()->request->baseUrl;?>/system/images/delete.jpg" /></a></span></h3>

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
		//生成一个管理的多选框
		array('class'=>'CCheckBoxColumn','name'=>'id'),
		'id',
		array(
			'name'=>'mname',
			'type'=>'raw',
			'value'=>'CHtml::link($data->mname,array(update,id=>$data->id))',
		),
		//'password',
		//'headimg',
		'email',
		//'qq',
		/*
		'telephone',
		'address',
		'real_name',
		'id_card',
		'ip',
		'login_time',
		*/
		array(
			'name'=>'login_time',
			'value'=>'date("Y-m-d H:i:s",$data->login_time)',
		),
		array(
			'name'=>'email_validate',
			'header'=>'邮箱验证',
			'filter'=>Yii::app()->params['email_validate'],
			'value'=>'Yii::app()->params[email_validate][$data->email_validate]',
		),
		//CButtonColumn对应admin按钮栏
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

