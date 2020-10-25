<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle =  '安装完成'  . ' - ' . Yii::app()->name;
//$this->breadcrumbs=array(
//	'Login',
//);
?>

    <div class="form form-signin">
    <?php $this->renderPartial('//layouts/flash-message'); ?>


      <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'link',
                'type'=>'primary',
                'url'=> $this->createUrl('site/index'),
                'label'=>'访问首页',
            )); ?>
      </div>

    </div><!-- form -->
