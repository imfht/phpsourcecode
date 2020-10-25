<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

//$this->pageTitle=Yii::app()->name . ' - Login';
//$this->breadcrumbs=array(
//	'Login',
//);
?>

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>'adminuser-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
    )); ?>

    <?php
    $roles = array_flip(Yii::app()->params['role']);

    echo $form->hiddenField($model, 'roleid', array(
        'value' => 1,
    ));
    ?>
      <?php echo $form->textFieldRow($model, 'username'); ?>
    <?php if ($this->action->id == 'update'): ?>
      <?php echo $form->passwordFieldRow($model, 'password', array(
          'value' => 'password',
      )); ?>
        <?php echo $form->passwordFieldRow($model, 'passwordAgain', array(
            'value' => 'password',
        )); ?>
    <?php elseif ($this->action->id == 'create'): ?>
        <?php echo $form->passwordFieldRow($model, 'password'); ?>
        <?php echo $form->passwordFieldRow($model, 'passwordAgain'); ?>
    <?php endif; ?>


    <?php if ($this->action->id == 'update'): ?>

    <?php echo $form->dropDownListRow($model, 'status', Yii::app()->params['statusAction']); ?>

    <?php endif; ?>

      <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>'确定',
            )); ?>
      </div>

    <?php $this->endWidget(); ?>

