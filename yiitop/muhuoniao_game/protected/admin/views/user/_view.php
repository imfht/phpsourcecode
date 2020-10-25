<tr class="ood">
    <td><?php echo CHtml::encode($data->id);?></td>
    <td><?php echo CHtml::link(CHtml::encode($data->username), array('update', 'id'=>$data->id)); ?></td>
    <td><?php $role=Yii::app()->params['role'];echo $role[$data->status];?></td>
    <td><?php echo CHtml::encode(date('Y-m-d H:i:s',$data->cteate_time)); ?></td>
    <th><?php echo CHtml::encode(date('Y-m-d H:i:s',$data->login_time))?></th>
    <th><?php echo CHtml::encode($data->ip);?></th>
  </tr>
