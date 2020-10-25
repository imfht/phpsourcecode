<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - 伪静态设置';
?>

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>'rewriteconfig-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
      'htmlOptions'=>array('enctype' => 'multipart/form-data'),
    )); ?>

      <?php echo $form->dropDownListRow($model, 'UrlSuffix', CMap::mergeArray(array('-1' => '请选择'), Yii::app()->params['urlSuffix'])); ?>
      <?php echo $form->textFieldRow($model, 'CategoryRule', array(
          'hint'=> "可用变量：{shorttitle}，表示分类别名 <br />示例：<br /> category/{shorttitle} <br /> fenlei/{shorttitle}"
      )); ?>
      <?php echo $form->textFieldRow($model, 'BookDetailRule', array(
          'hint'=> "可用变量：{id}，表示小说编号 <br />示例：<br /> book/{id} <br /> novel/{id}"
      )); ?>
      <?php echo $form->textFieldRow($model, 'ChapterDetailRule', array(
          'hint'=> "可用变量：{id}，表示章节编号 <br />示例：<br /> chapter/{id} <br /> zhangjie/{id}"
      )); ?>
      <?php echo $form->textFieldRow($model, 'NewsListRule', array(
          'hint'=> "可用变量：{id}，表示新闻分类编号 <br />示例：<br /> news/list-{id} <br /> xinwen/list-{id}"
      )); ?>
      <?php echo $form->textFieldRow($model, 'NewsDetailRule', array(
          'hint'=> "可用变量：{id}，表示新闻内容编号 <br />示例：<br /> news/{id} <br /> xinwen/2013/{id}"
      ));  ?>
<!--      --><?php //echo $form->textFieldRow($model, 'SiteAttachmentPath'); ?>
<!--      --><?php //echo $form->textAreaRow($model, 'SiteCopyright'); ?>


      <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>'确定',
            )); ?>
      </div>

    <?php $this->endWidget(); ?>

