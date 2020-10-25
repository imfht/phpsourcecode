<?php
/* @var $this SiteController */

$this->pageTitle = "小说章节管理" . " - " . Yii::app()->name;
?>

<?php if ($book != null): ?>

    <?php
    $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
        'homeLink' => CHtml::tag("a", array('href' => $this->createUrl('book/index')), $book->title),
        'links'=> array(
            '章节管理'
        ),
    ));
    ?>

    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=> '添加小说章节',
        'url' => $this->createUrl('article/create', array('bid' => $book->id)),
        'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
        'size'=>'null', // null, 'large', 'small' or 'mini'
    )); ?>

<?php endif; ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'chapter-grid',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{items}\n{pager}",
    'enablePagination' => true,
    'filter' => $model,
    'columns'=>array(
        array('name'=>'id', 'header'=>'内容编号'),
        array('name'=>'chapter'),
        array('name'=>'title', ),
        array('name'=>'bookid', 'value' => '$data->book->title', 'filter' => false),
        array('name'=>'createtime', 'value' => 'date("Y-m-d H:i:s", $data->createtime)', 'filter' => false),
//        array('name'=>'language', 'header'=>'Language'),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>"{update}{delete}",
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>
