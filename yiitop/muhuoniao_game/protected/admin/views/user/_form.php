<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>
<table width="900" border="0" class="table_b">
 <?php if($model->isNewRecord):?>
  <tr>
    <th>用户名：</th>
    <td><?php echo $form->textField($model,'username',array('maxlength'=>30,'class'=>'text','value'=>'')); ?></td>
  </tr>
  <?php endif;?>
  <tr>
    <th>密码：</th>
    <td><?php echo $form->passwordField($model,'password',array('maxlength'=>255,'class'=>'text','value'=>'')); ?></td>
  </tr>
    <tr>
    <th>确认密码：</th>
    <td><input type="password" class="text" /></td>
  </tr>
  <tr>
    <th>管理权限：</th>
    <td>
    <?php echo $form->dropDownList($model,'status',Yii::app()->params['role'],array('class'=>'select')); ?>
    
    </td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <td><?php echo CHtml::submitButton($model->isNewRecord ? '创建用户' : '确认修改'); ?></td>
  </tr>
</table>

<?php $this->endWidget(); ?>
