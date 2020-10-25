<!---------------side_right---------------->

<div id="side_right">
<h2><strong>订单管理</strong></h2>
<h3><span class="title">订单<strong><?php echo $model->order_number;?></strong>详情</span></h3>

<div class="create">

<form>
<table width="900" border="0" class="table_b">
 <tr>
    <th>订单ID：</th>
    <td><?php echo $model->id?></td>
  </tr>
  <tr>
    <th>订单金额：</th>
    <td><?php echo $model->price;?></td>
  </tr>
  <tr>
    <th>Mid：</th>
    <td><?php echo $model->memberName->mname;?></td>
  </tr>
  <tr>
    <th>充值游戏：</th>
    <td><?php echo $model->gameName->gname;?></td>
  </tr>
    <tr>
    <th>充值大区：</th>
    <td><?php echo $model->gid_server_id;?></td>
  </tr>
    <tr>
    <th>Pay 类型：</th>
    <td><?php echo $model->orderType->name;?></td>
  </tr>
      <tr>
    <th>Pay 时间：</th>
    <td><?php echo Order::model()->getTime($data->pay_time);?></td>
  </tr>
  <tr>
    <th>Pay IP：</th>
    <td><?php echo $model->pay_ip;?></td>
  </tr>
  <tr>
    <th>是否支付：</th>
    <td><?php if($model->pay==0){echo '未支付';}else{echo '已支付';}?></td>
  </tr>
    <tr>
    <th>编辑：</th>
    <td><div class="update"><img src="<?php echo Yii::app()->baseUrl;?>/system/images/update.png" /><span><?php echo CHtml::link('修改信息',array('update','id'=>$model->id))?></span><img src="<?php echo Yii::app()->baseUrl;?>/system/images/delete.png" /><span><?php echo CHtml::link('删除订单',array('delete','id'=>$model->id),array('confirm'=>'确认删除此用户？'))?></span></div></td>
  </tr>
</table>


</form>

</div>






</div>
<!---------------side_right end---------------->

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'order_number',
		'mid',
		'gid',
		'gid_server_id',
		'price',
		'pay_type',
		'pay_time',
		'pay_ip',
		'pay',
	),
)); ?>
