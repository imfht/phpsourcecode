<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'article-type-form',
	'enableAjaxValidation'=>false,
)); ?>
<table width="900" border="0" class="table_b">
  <tr>
    <th>栏目：</th>
    <td><?php echo $form->dropDownList($model,'tid', ArticleType::model()->getArticleType(),array('class'=>'select')); ?></td>
  </tr>
  <tr>
    <th>文章类型名称：</th>
    <td><?php echo $form->textField($model,'typename',array('maxlength'=>50,'class'=>'text')); ?></td>
  </tr>
  	<tr>
    <th>&nbsp;</th>
    <td><?php echo CHtml::submitButton($model->isNewRecord ? '添加栏目' : '确认修改'); ?></td>
  </tr>
</table>


<?php $this->endWidget(); ?>
