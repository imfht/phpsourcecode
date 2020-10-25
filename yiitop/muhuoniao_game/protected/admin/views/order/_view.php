<tr class="ood">
    <td><?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?></td>
    <td><?php echo CHtml::link(CHtml::encode($data->order_number), array('view', 'id'=>$data->id)); ?></td>
    <td><?php echo CHtml::encode($data->memberName->mname); ?></td>
    <td><?php echo CHtml::encode($data->gameName->gname); ?></td>
    <td><?php echo CHtml::encode(Games::model()->getGamesServerValue($data->gid,$data->gid_server_id)); ?>区</td>
    <td><?php echo CHtml::encode($data->price); ?></td>
    <td><?php echo CHtml::encode($data->orderType->name); ?></td>

