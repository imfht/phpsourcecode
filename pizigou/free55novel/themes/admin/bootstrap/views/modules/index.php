<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'ajaxButton',
    'label'=>'查找本地模块',
    'url' => $this->createUrl('modules/scan'),
    'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'null', // null, 'large', 'small' or 'mini'
    'ajaxOptions' => array(
        'success' => 'js:function(r){alert(r);window.location.reload();}',
        'error' => 'js:function(){alert("查找本地目录出现错误，请联系开发者！");}',
    ),
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'filter' => $model,
    'columns'=>array(
//        array('name'=>'id', 'header'=>'#'),
        array('name'=>'id', 'header' => '模块编号', 'filter' => false),
        array('name'=>'title', ),
        array('name'=>'version', ),
//        array(
//            'name'=>'imgurl',
//            'type' => 'html',
//            'value' => 'CHtml::image(H::getNovelImageUrl($data->imgurl), "", array("style"=>"width: 50px;height:50px"))',
//            'htmlOptions'=>array('style'=>'width: 20px;height:20px'),
//             'filter' => false
//        ),
        array('name'=>'author', ),
        array(
            'name'=>'status',
//            'type' => 'raw',
            'value' => 'Yii::app()->params[moduleStatus][$data->status]',
        ),
//        array('name'=>'fwversion', ),
//        array('name'=>'cid', 'value' => '$data->category->title', 'filter' => $categorys),
//        array('name'=> 'recommendlevel', 'value' => 'Yii::app()->params["recommendLevel"][$data->recommendlevel]', 'filter' => Yii::app()->params["recommendLevel"]),
//        array('name'=>'language', 'header'=>'Language'),
//        array('name'=>'createtime', 'value' => 'date("Y-m-d H:i:s", $data->createtime)', 'filter' => false),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>"{setup}{start}{stop}{delete}",
            'htmlOptions'=>array('style'=>'width: 50px'),
            'buttons' => array(
                'setup' => array(
                    'label'=>'安装该模块',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("modules/setup",array("id"=>$data->id))',       // a PHP expression for generating the URL of the button
                    'imageUrl'=> '',  // image URL of the button. If not set or false, a text link is used
                    'icon' => 'plus',
                    'options'=> array('style'=>'cursor:pointer;'), // HTML options for the button tag
                    'click'=> 'js:function () {ajaxPost($(this).attr("href"));return false;}',     // a JS function to be invoked when the button is clicked
                    'visible'=> '$data->status == 0 ? true : false',
                ),
                'start' => array(
                    'label'=>'启用该模块',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("modules/start",array("id"=>$data->id))',       // a PHP expression for generating the URL of the button
                    'imageUrl'=> '',  // image URL of the button. If not set or false, a text link is used
                    'icon' => 'play',
                    'options'=> array('style'=>'cursor:pointer;'), // HTML options for the button tag
                    'click'=> 'js:function () {ajaxPost($(this).attr("href"));return false;}',     // a JS function to be invoked when the button is clicked
                    'visible'=> '$data->status == -1 ? true : false',
                ),
                'stop' => array(
                    'label'=>'停止该模块',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("modules/stop",array("id"=>$data->id))',       // a PHP expression for generating the URL of the button
                    'imageUrl'=> '',  // image URL of the button. If not set or false, a text link is used
                    'icon' => 'off',
                    'options'=> array('style'=>'cursor:pointer;'), // HTML options for the button tag
                    'click'=> 'js:function () {ajaxPost($(this).attr("href"));return false;}',     // a JS function to be invoked when the button is clicked
                    'visible'=> '$data->status == 1 ? true : false',
                ),
                'delete' => array(
                    'label'=>'删除该模块',     // text label of the button
                    'url'=>'Yii::app()->controller->createUrl("modules/delete",array("id"=>$data->id))',       // a PHP expression for generating the URL of the button
                    'imageUrl'=> '',  // image URL of the button. If not set or false, a text link is used
//                    'icon' => 'stop',
                    'options'=> array('style'=>'cursor:pointer;'), // HTML options for the button tag
//                    'click'=> 'js:function(){}',     // a JS function to be invoked when the button is clicked
                    'visible'=> 'true',
                ),
            ),
        ),
    ),
)); ?>
<script>
    function ajaxPost(url)
    {
        $.post(url, function(r) {
            alert(r);
            window.location.reload();
        });
    }
</script>
