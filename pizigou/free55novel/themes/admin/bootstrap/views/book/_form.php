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
      'id'=>'book-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
      'htmlOptions'=>array('enctype' => 'multipart/form-data'),
    )); ?>

      <?php echo $form->textFieldRow($model, 'title'); ?>
      <?php echo $form->textFieldRow($model, 'author'); ?>
    <?php echo $form->textFieldRow($model, 'keywords', array(
        'hint'=> '提示：逗号分隔'
    )); ?>
      <?php echo $form->dropDownListRow($model, 'cid', $categorys); ?>
      <?php echo $form->dropDownListRow($model, 'type', Yii::app()->params['novelType']); ?>
      <?php echo $form->fileFieldRow($model, 'imagefile'); ?>

      <?php if ($this->action->id == 'update'):?>
      <div class="control-group ">
          <label for="Book_imagefile1" class="control-label">封面图</label>
          <div class="controls">
              <?php echo CHtml::image(H::getNovelImageUrl($model->imgurl));?>
          </div>
      </div>
      <?php endif; ?>

      <?php echo $form->textAreaRow($model, 'summary'); ?>
<!--    <div class="control-group ">-->
<!--        <label for="Book_imagefile1" class="control-label">简介</label>-->
<!--        <div class="controls">-->
      <?php
//        $this->widget('application.extensions.tinymce.ETinyMce', array(
//            'model' => $model,
//            'mode' => 'html',
//            'attribute' =>  'summary',
//            'editorTemplate' => 'full',
//            'language' => 'cn',
//            'htmlOptions' => array('rows' => 10, 'cols' => 50)
//        ));
      ?>
      <?php
      $this->widget('ext.ueditor.Ueditor',
          array(
              'getId'=>'Book_summary',
              'UEDITOR_HOME_URL'=>"/",
              'options'=>'
                    wordCount:false,
                    elementPathEnabled:false,
                    imagePath:"/upload/ueditor/"
                    ',
          ));
      ?>
<!--        </div>-->
<!--    </div>-->
<!--      --><?php //echo $form->textAreaRow($model, 'sections', array(
//          'hint' => '每个分卷一行',
//          'cols' => 80,
//          'rows' => 10,
//      )); ?>
<!--      --><?php //echo $form->textFieldRow($model, 'tags', array(
//          'hint'=> '提示：逗号分隔'
//      )); ?>


    <?php echo $form->dropDownListRow($model, 'recommendlevel', Yii::app()->params['recommendLevel']); ?>


      <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>'确定',
            )); ?>
      </div>

    <?php $this->endWidget(); ?>

