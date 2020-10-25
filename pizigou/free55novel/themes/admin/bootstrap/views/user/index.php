<?php
/* @var $this SiteController */

$this->pageTitle = "会员管理" . " - " . Yii::app()->name;
?>

<?php //$this->widget('bootstrap.widgets.TbButton', array(
//    'label'=>'新建小说',
//	'url' => $this->createUrl('article/create'),
//    'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
//    'size'=>'null', // null, 'large', 'small' or 'mini'
//)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=> $dataProvider,
    'template'=>"{items}",
    'filter' => $model,
    'columns'=>array(
//        array('name'=>'id', 'header'=>'#'),
        array('name'=> 'id', 'header' => '#', 'filter' => false),
        array('name'=> 'username', ),
        array('name'=> 'createtime', 'value' => 'date("Y-m-d H:i:s", $data->createtime)', 'filter' => false),
        array('name'=> 'lastlogintime', 'value' => 'date("Y-m-d H:i:s", $data->lastlogintime)', 'filter' => false),
        array('name'=> 'status', 'value' => 'Yii::app()->params["statusLabel"][$data->status]', 'filter' => Yii::app()->params['statusAction']),
//        array('name'=>'bookid', 'value' => '$data->book->title'),
//        array('name'=>'language', 'header'=>'Language'),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>"{update}{delete}",
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
