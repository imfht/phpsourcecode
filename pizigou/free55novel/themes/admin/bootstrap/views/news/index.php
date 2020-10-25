<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'发布新闻',
    'url' => $this->createUrl('news/create'),
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
        array('name'=>'id', 'header' => '编号', 'filter' => false),
        array('name'=>'title', ),
        array(
            'name'=>'imgurl',
            'type' => 'html',
            'value' => 'CHtml::image(H::getNovelImageUrl($data->imgurl), "", array("style"=>"width: 50px;height:50px"))',
            'htmlOptions'=>array('style'=>'width: 20px;height:20px'),
             'filter' => false
        ),
//        array('name'=>'author', ),
        array('name'=>'cid', 'value' => '$data->category->title', 'filter' => $categorys),
        array('name'=>'createtime', 'value' => 'date("Y-m-d H:i:s", $data->createtime)', 'filter' => false),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>"{view}{update}{delete}",
            'htmlOptions'=>array('style'=>'width: 50px'),
            'buttons' => array(
                'view' => array(
                    'label'=>'浏览新闻',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("news/view",array("id"=>$data->id))',       // a PHP expression for generating the URL of the button
                    'imageUrl'=> '',  // image URL of the button. If not set or false, a text link is used
                    'icon' => 'eye-open',
                    'options'=> array('style'=>'cursor:pointer;'), // HTML options for the button tag
                    'click'=> 'js:function(){}',     // a JS function to be invoked when the button is clicked
                    'visible'=> 'true',
                ),
            ),
        ),
    ),
)); ?>
