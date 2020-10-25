<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
$options = array(
  0 => "未启用",
  1 => "启用"
);
?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'创建广告',
    'url' => $this->createUrl('ads/create'),
    'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'null', // null, 'large', 'small' or 'mini'
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
//    'filter' => $model,
    'columns'=>array(
//        array('name'=>'id', 'header'=>'#'),
        array('name'=>'id', 'header' => '广告编号', 'filter' => false),
//        array('name'=>'title', ),
        array(
            'name'=>'模板中广告调用',
//            'type' => 'html',
            'value' => '"{novel_ads_link id=$data->id}"',
//            'htmlOptions'=>array('style'=>'width: 20px;height:20px'),
             'filter' => false
        ),
        array(
            'name'=>'外部JS广告调用',
            'type' => 'raw',
            'value' => 'CHtml::textField("jsCall", H::getNovelImageUrl("/showjs?id=" . $data->id))',
//            'htmlOptions'=>array('style'=>'width: 20px;height:20px'),
            'filter' => false
        ),
//        array('name'=>'author', ),
//        array('name'=>'cid', 'value' => '$data->category->title', 'filter' => $categorys),
//        array('name'=> 'recommendlevel', 'value' => 'Yii::app()->params["recommendLevel"][$data->recommendlevel]', 'filter' => Yii::app()->params["recommendLevel"]),
////        array('name'=>'language', 'header'=>'Language'),
        array('name'=>'createtime', 'value' => 'date("Y-m-d H:i:s", $data->createtime)', 'filter' => false),
        array('name'=>'status', 'value' => 'Yii::app()->params["novelAdsStatus"][$data->status]', 'filter' => false),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>"{update}{delete}",
//            'htmlOptions'=>array('style'=>'width: 50px'),
//            'buttons' => array(
//                'view' => array(
//                    'label'=>'查看小说章节',     // text label of the button
//                    'url'=>'Yii::app()->controller->createUrl("article/index",array("bid"=>$data->id))',       // a PHP expression for generating the URL of the button
//                    'imageUrl'=> '',  // image URL of the button. If not set or false, a text link is used
//                    'icon' => 'eye-open',
//                    'options'=> array('style'=>'cursor:pointer;'), // HTML options for the button tag
//                    'click'=> 'js:function(){}',     // a JS function to be invoked when the button is clicked
//                    'visible'=> 'true',
//                ),
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
