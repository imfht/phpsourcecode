<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - 编辑新闻';
//$this->breadcrumbs=array(
//	'Login',
//);
?>


<?php $this->renderPartial('_form', array(
    'model' => $model,
    'categorys' => $categorys,
)); ?>

