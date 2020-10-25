<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'新建新闻分类',
	'url' => $this->createUrl('newscategory/create'),
    'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'null', // null, 'large', 'small' or 'mini'
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'filter' => $model,
    'columns'=>array(
//        array('name'=>'id', 'header'=>'#'),
        array('name'=>'id', 'header' => '#', 'filter' => false),
        array('name'=>'title', ),
        array('name'=>'parentid', 'value' => '$data->parent->title', 'filter' => false),
        array('name'=>'sort',  'filter' => false),
//        array('name'=>'language', 'header'=>'Language'),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>"{update}{delete}",
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
