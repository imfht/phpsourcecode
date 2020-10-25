<tr class="ood">
    <td><?php echo CHtml::encode($data->id); ?></td>
    <td><?php echo CHtml::link(CHtml::encode($data->mname), array('view', 'id'=>$data->id)); ?></td>
    <td><?php echo CHtml::encode($data->login_time); ?></td>
    <td><?php echo CHtml::encode($data->id_card); ?></td>
    <td><?php echo CHtml::encode($data->email); ?></td>
    <td><?php echo CHtml::encode($data->qq); ?></td>
  </tr>
 