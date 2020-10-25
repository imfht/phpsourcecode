<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'添加友情链接',
    'url' => $this->createUrl('friendlink/create'),
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
        array('name'=>'sort', 'filter' => false),
//        array('name'=>'language', 'header'=>'Language'),
        array('name'=>'createtime', 'value' => 'date("Y-m-d H:i:s", $data->createtime)', 'filter' => false),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>"{update}{delete}",
            'htmlOptions'=>array('style'=>'width: 50px'),
//            'buttons' => array(
//                'add' => array(
//                    'label'=>'添加小说章节',     // text label of the button
//                    'url'=>'Yii::app()->controller->createUrl("article/create",array("bid"=>$data->id))',       // a PHP expression for generating the URL of the button
//                    'imageUrl'=> '',  // image URL of the button. If not set or false, a text link is used
//                    'icon' => 'plus',
//                    'options'=> array('style'=>'cursor:pointer;'), // HTML options for the button tag
//                    'click'=> 'js:function(){}',     // a JS function to be invoked when the button is clicked
//                    'visible'=> 'true',
//                ),
//            ),
        ),
    ),
)); ?>
