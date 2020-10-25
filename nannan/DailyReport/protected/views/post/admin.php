<?php
/* @var $this PostController */
/* @var $model Post */

$this->breadcrumbs=array(
	'Posts'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Post', 'url'=>array('index')),
	array('label'=>'Create Post', 'url'=>array('create')),
);
?>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'post-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'content',
		'author_id',
		'post_time',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
