<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle =  '第一步 数据库安装'  . ' - ' . Yii::app()->name;
//$this->breadcrumbs=array(
//	'Login',
//);
?>


  

    <div class="form form-signin">
    <?php $this->renderPartial('//layouts/flash-message'); ?>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>'setup-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
    )); ?>
        <fieldset class="well the-fieldset">
            <legend class="the-legend"><b>第一步 数据库安装</b></legend>
        <?php echo $form->textFieldRow($model,'dbhost'); ?>
        <?php echo $form->textFieldRow($model,'dbname', array(
            'hint' => '提示：数据库不存在会自动创建',
        )); ?>

      <?php echo $form->textFieldRow($model,'username'); ?>

      <?php echo $form->passwordFieldRow($model,'password',array(
            'hint'=>' ',
        )); ?>
        <?php echo $form->passwordFieldRow($model,'repassword',array(
            'hint'=>' ',
        )); ?>
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
