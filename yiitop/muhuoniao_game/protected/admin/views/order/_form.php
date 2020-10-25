<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'order-form',
	'enableAjaxValidation'=>false,
)); ?>
<table width="900" border="0" class="table_b">
  <tr>
    <th>订单编号：</th>
    <td><?php  echo $form->textField($model,'order_number'); ?></td>
  </tr>

  <tr>
    <th>充值游戏：</th>
    <td><?php echo $form->dropDownList($model,'gid',CHtml::listData(Games::model()->findAll(), 'id', 'gname'),array('class'=>'select')); ?></td>
  </tr>
    <tr>
    <th>充值大区：</th>
    <td><?php echo $form->textField($model,'gid_server_id',array('size'=>6,'maxlength'=>6)); ?></td>
  </tr>
  <tr>
    <th>充值金额：</th>
    <td><?php echo $form->textField($model,'price',array('size'=>11,'maxlength'=>11)); ?></td>
  </tr>
    <tr>
    <th>Pay 类型：</th>
    <td><?php echo $form->dropDownList($model,'pay_type',CHtml::listData(OrderType::model()->findAll(), 'id', 'name'),array('class'=>'select')); ?></td>
  </tr>
  <tr>
    <th>是否支付：</th>
    <td><?php echo $form->dropDownList($model,'pay',array('未支付','已支付'),array('class'=>'select')); ?></td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <td><?php echo CHtml::submitButton($model->isNewRecord ? '确认添加' : '确认修改'); ?></td>
  </tr>
</table>


<?php $this->endWidget(); ?>
