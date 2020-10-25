<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle =  '第二步 站点信息'  . ' - ' . Yii::app()->name;
?>
<div class="form form-signin">
    <?php $this->renderPartial('//layouts/flash-message'); ?>
    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>'baseconfig-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
      'htmlOptions'=>array('enctype' => 'multipart/form-data'),
    )); ?>
    <fieldset class="well the-fieldset">
        <legend class="the-legend"><b>第二步 站点信息</b></legend>
          <?php echo $form->textFieldRow($model, 'SiteName'); ?>
          <?php echo $form->dropDownListRow($model, 'SiteTheme', CMap::mergeArray(array('' => '请选择'),SystemBaseConfig::getThemeList())); ?>
          <?php echo $form->textFieldRow($model, 'SiteAdminEmail'); ?>
          <?php echo $form->textAreaRow($model, 'SiteKeywords', array(
            'hint'=> '多个关键字逗号分隔'
        )); ?>
          <?php echo $form->textAreaRow($model, 'SiteIntro'); ?>
    <!--      --><?php //echo $form->textFieldRow($model, 'SiteAttachmentPath'); ?>
          <?php echo $form->textAreaRow($model, 'SiteCopyright'); ?>
    </fieldset>

      <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>'下一步',
            )); ?>
      </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->