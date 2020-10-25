<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

//$this->pageTitle=Yii::app()->name . ' - Login';
//$this->breadcrumbs=array(
//	'Login',
//);
?>
<style type="text/css">
    .the-legend {
        border-style: none;
        border-width: 0;
        font-size: 14px;
        line-height: 20px;
        margin-bottom: 0;
    }
    .the-fieldset {
        border: 2px groove threedface #444;
        -webkit-box-shadow:  0px 0px 0px 0px #000;
        box-shadow:  0px 0px 0px 0px #000;
    }
</style>

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>'newscategory-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
    )); ?>

      <?php echo $form->textFieldRow($model, 'title'); ?>

        <?php echo $form->dropDownListRow($model, 'parentid', $categorys); ?>

        <?php echo $form->textFieldRow($model, 'shorttitle', array('hint' => '不填写则自动获取栏目名称的拼音')); ?>

        <?php echo $form->textFieldRow($model, 'sort', array(
            'hint'=> '提示：数值越大越靠前'
        )); ?>

        <fieldset class="well the-fieldset">
            <legend class="the-legend">分类 SEO 设置</legend>
            <?php echo $form->textFieldRow($model, 'seotitle'); ?>
            <?php echo $form->textFieldRow($model, 'keywords'); ?>
            <?php echo $form->textAreaRow($model, 'description'); ?>
        </fieldset>

      <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>'确定',
            )); ?>
      </div>

    <?php $this->endWidget(); ?>

