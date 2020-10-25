<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit', array(
    'heading'=> Yii::app()->name,
)); ?>

<?php //$this->widget('bootstrap.widgets.TbLabel', array(
//        'type'=>'success', // 'success', 'warning', 'important', 'info' or 'inverse'
//        'label'=> Yii::app()->user->name,
//    )); ?><!-- -->
<p><?php echo Yii::app()->user->name;?>,您好！欢迎使用<?php echo Yii::app()->name;?> ,开源、免费、强大的小说系统！<br /> 当前系统版本：<b><?php echo FWXSVersion;?></b><br />我们的官方网站：<a href="http://www.free55.net" target="_blank">http://www.free55.net/</a></p>

<p><?php $this->widget('bootstrap.widgets.TbButton', array(
        'type' => 'primary',
        'size' => 'normal',
        'label' => '获取更多帮助',
        'url' => 'http://www.free55.net/',
        'htmlOptions' => array('target' => '_blank'),
    )); ?></p>

<?php $this->endWidget(); ?>
