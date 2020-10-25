<tr class="ood">
    <td><?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?></td>
    <td><?php echo CHtml::link(CHtml::encode($data->gameName->gname), array('view', 'id'=>$data->id)); ?></td>
    <td><?php echo CHtml::encode($data->userid); ?></td>
    <td><?php echo CHtml::encode($data->username); ?></td>
    <td><?php echo CHtml::encode($data->password); ?></td>
    </tr>
