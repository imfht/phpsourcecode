<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'member-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>
<table width="900" border="0"  class="table_b">
  <tr>
    <th>用户名：</th>
    <td><?php echo $form->textField($model,'mname',array('size'=>20,'class'=>'text')); ?></td>
  </tr>
    <tr>
    <th>密码：</th>
    <td><?php echo $form->passwordField($model,'password',array('class'=>'text','maxlength'=>50)); ?></td>
  </tr>
      <tr>
    <th>确认密码：</th>
    <td><input type="password" class="text" /></td>
  </tr>
  <tr>
    <th>头像：</th>
    <td><?php echo CHtml::activeFileField($model,'headimg',array('class'=>'file')); ?>
    <p>
    <a href="#">显示头像</a>
    </p>
    
    
    </td>
  </tr>
  <tr>
    <th>邮箱：</th>
    <td><?php echo $form->textField($model,'email',array('size'=>40,'maxlength'=>30,'class'=>'text')); ?></td>
  </tr>
    <tr>
    <th>QQ：</th>
    <td><?php echo $form->textField($model,'qq',array('class'=>'text')); ?> </td>
  </tr>
  <tr>
    <th>电话：</th>
    <td><?php echo $form->textField($model,'telephone',array('size'=>15,'maxlength'=>15,'class'=>'text')); ?>
    
    
    </td>
  </tr>
   <tr>
    <th>地址：</th>
    <td>
    <?php echo $form->textField($model,'address',array('size'=>60,'maxlength'=>100,'class'=>'text')); ?>
    
    </td>
 
  </tr>

   <tr>
    <th>真实姓名：</th>
    <td><?php echo $form->textField($model,'real_name',array('size'=>15,'maxlength'=>15,'class'=>'text')); ?></td>
  </tr>

     <tr>
    <th>身份证号：</th>
    <td><?php echo $form->textField($model,'id_card',array('size'=>25,'maxlength'=>25,'class'=>'text')); ?></td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <td><?php echo CHtml::submitButton($model->isNewRecord ? '创建会员' : '确认修改'); ?></td>
  </tr>
</table>


<?php $this->endWidget(); ?>
