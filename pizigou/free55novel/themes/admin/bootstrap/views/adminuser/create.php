<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - 添加管理员';
//$this->breadcrumbs=array(
//	'Login',
//);
?>

<?php $this->renderPartial('_form', array(
    'model' => $model,
//    'book' => $book,
)); ?>

