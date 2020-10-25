<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

//$this->pageTitle=Yii::app()->name . ' - Login';
//$this->breadcrumbs=array(
//	'Login',
//);
?>
<!--    --><?php
//    $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
//        'homeLink' => CHtml::tag("a", array('href' => $this->createUrl('book/index')), $book->title),
//        'links'=> array(
//            $this->action->id == 'create' ? '添加章节': '修改章节',
//        ),
//    ));
//    ?>
    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>'categor-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
    )); ?>

    <div class="control-group ">
        <label for="Article_book" class="control-label">所属小说</label>
        <div class="controls">
            <?php if ($this->action->id == 'create'): ?>

                <?php $this->widget('bootstrap.widgets.TbLabel', array(
                    'type'=> 'success', // 'success', 'warning', 'important', 'info' or 'inverse'
                    'label'=> $book->title,
                )); ?>
            <?php else: ?>
                <?php $this->widget('bootstrap.widgets.TbLabel', array(
                    'type'=>'success', // 'success', 'warning', 'important', 'info' or 'inverse'
                    'label'=> $model->book->title,
                )); ?>
            <?php endif; ?>
        </div>
    </div>

      <?php echo $form->textFieldRow($model, 'title'); ?>
<!--      --><?php //echo $form->textFieldRow($model, 'chapternum'); ?>
      <?php echo $form->hiddenField($model, 'bookid'); ?>

<!--        --><?php //echo $form->dropDownListRow($model, 'parentid', $categorys); ?>



    <?php echo $form->textFieldRow($model, 'chapter'); ?>

    <?php echo $form->textAreaRow($model, 'content'); ?>

    <?php
    $this->widget('ext.ueditor.Ueditor',
        array(
            'getId'=>'Article_content',
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

