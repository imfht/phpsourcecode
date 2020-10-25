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
      'id'=>'friendlink-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
      'htmlOptions'=>array('enctype' => 'multipart/form-data'),
    )); ?>

      <?php echo $form->textFieldRow($model, 'title'); ?>
      <?php echo $form->textFieldRow($model, 'linkurl'); ?>

      <?php echo $form->fileFieldRow($model, 'imagefile'); ?>
      <?php if ($this->action->id == 'update'):?>
      <div class="control-group ">
          <label for="FriendLink_imagefile1" class="control-label">站点LOGO</label>
          <div class="controls">
              <?php echo CHtml::image(H::getNovelImageUrl($model->imgurl));?>
          </div>
      </div>
      <?php endif; ?>

    <?php echo $form->textFieldRow($model, 'sort', array(
        'hint'=> '提示：数值越大越靠前'
    )); ?>


      <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>'确定',
            )); ?>
      </div>

    <?php $this->endWidget(); ?>

