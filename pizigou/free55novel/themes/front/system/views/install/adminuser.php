<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle =  '第三步 管理员账号'  . ' - ' . Yii::app()->name;
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
        <legend class="the-legend"><b>第三步 管理员账号</b></legend>

        <?php
//        $roles = array_flip(Yii::app()->params['role']);
        echo $form->hiddenField($model, 'roleid', array(
            'value' => 1,
        ));
        ?>
        <?php echo $form->textFieldRow($model, 'username'); ?>
        <?php echo $form->passwordFieldRow($model, 'password'); ?>
        <?php echo $form->passwordFieldRow($model, 'passwordAgain'); ?>

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