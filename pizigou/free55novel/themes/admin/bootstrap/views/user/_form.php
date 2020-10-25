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
      'id'=>'user-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
    )); ?>

      <?php echo $form->textFieldRow($model, 'username'); ?>
      <?php echo $form->passwordFieldRow($model, 'password', array(
          'value' => 'password',
      )); ?>
<!--      --><?php //echo $form->hiddenField($model, 'bookid'); ?>

<!--        --><?php //echo $form->dropDownListRow($model, 'parentid', $userys); ?>

<!--    <div class="control-group ">-->
<!--        <label for="Article_book" class="control-label">所属小说</label>-->
<!--        <div class="controls">-->
<!--         --><?php //if ($this->action->id == 'create'): ?>
<!---->
<!--             --><?php //$this->widget('bootstrap.widgets.TbLabel', array(
//                 'type'=> 'success', // 'success', 'warning', 'important', 'info' or 'inverse'
//                 'label'=> $book->title,
//             )); ?>
<!--         --><?php //else: ?>
<!--             --><?php //$this->widget('bootstrap.widgets.TbLabel', array(
////                 'type'=>'success', // 'success', 'warning', 'important', 'info' or 'inverse'
////                 'label'=> $model->book->title,
////             )); ?>
<!--         --><?php //endif; ?>
<!--        </div>-->
<!--    </div>-->

    <?php echo $form->dropDownListRow($model, 'status', Yii::app()->params['statusAction']); ?>

<!--    --><?php //echo $form->textAreaRow($model, 'content'); ?>
<!---->
<!--    --><?php
//    $this->widget('ext.ueditor.Ueditor',
//        array(
//            'getId'=>'Article_content',
//            'UEDITOR_HOME_URL'=>"/",
//            'options'=>'
//                        wordCount:false,
//                        elementPathEnabled:false,
//                        imagePath:"/upload/ueditor/"
//                        ',
//        ));
//    ?>

      <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>'确定',
            )); ?>
      </div>

    <?php $this->endWidget(); ?>

