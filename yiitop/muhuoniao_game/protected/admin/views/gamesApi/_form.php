<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'games-api-form',
	'enableAjaxValidation'=>false,
)); ?>
<table width="900" border="0" class="table_b">
  <tr>
    <th>游戏名称：</th>
    <td><?php echo $form->dropDownList($model,'gid', Games::model()->getGamesAll(),array('class'=>'select')); ?></td>
  </tr>
  <tr>
    <th>平台服务器的注册用户编号：</th>
    <td><?php echo $form->textField($model,'userid',array('class'=>'text')); ?></td>
  </tr>
  <tr>
    <th>平台服务器的通行证帐号：</th>
    <td><?php echo $form->textField($model,'username',array('size'=>50,'maxlength'=>50,'class'=>'text')); ?></td>
  </tr>
    <tr>
    <th>密钥：</th>
    <td><?php echo $form->textField($model,'password',array('size'=>60,'maxlength'=>255,'class'=>'text')); ?></td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <td><?php echo CHtml::submitButton($model->isNewRecord ? '确认添加' : '确认修改'); ?></td>
  </tr>
</table>


<?php $this->endWidget(); ?>