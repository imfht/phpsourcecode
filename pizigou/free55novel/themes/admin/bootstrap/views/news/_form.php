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
      'id'=>'news-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
      'htmlOptions'=>array('enctype' => 'multipart/form-data'),
    )); ?>

      <?php echo $form->textFieldRow($model, 'title'); ?>

      <?php echo $form->dropDownListRow($model, 'cid', $categorys); ?>

      <?php echo $form->textFieldRow($model, 'keywords', array(
          'hint'=> '提示：多个关键字逗号分隔'
      )); ?>

      <?php echo $form->textFieldRow($model, 'author'); ?>

      <?php echo $form->fileFieldRow($model, 'imagefile'); ?>

      <?php if ($this->action->id == 'update'):?>
      <div class="control-group ">
          <label for="News_imagefile1" class="control-label">封面图</label>
          <div class="controls">
              <?php echo CHtml::image(H::getNovelImageUrl($model->imgurl));?>
          </div>
      </div>
      <?php endif; ?>

      <?php echo $form->textAreaRow($model, 'summary'); ?>
      <?php
      $this->widget('ext.ueditor.Ueditor',
          array(
              'getId'=>'News_summary',
              'UEDITOR_HOME_URL'=>"/",
              'options'=>'
                    wordCount:false,
                    elementPathEnabled:false,
                    imagePath:"/upload/ueditor/"
                    ',
          ));
      ?>

    <?php echo $form->textAreaRow($model, 'content'); ?>

    <?php
    $this->widget('ext.ueditor.Ueditor',
        array(
            'getId'=>'News_content',
            'UEDITOR_HOME_URL'=>"/",
            'options'=>'
                        wordCount:false,
                        elementPathEnabled:false,
                        imagePath:"/upload/ueditor/"
                        ',
        ));
    ?>


      <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>'确定',
            )); ?>
      </div>

    <?php $this->endWidget(); ?>

