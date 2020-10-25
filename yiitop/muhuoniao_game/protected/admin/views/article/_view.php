 <tr class="ood">
    <td><?php echo CHtml::encode($data->id); ?></td>
    <td><?php echo CHtml::link(CHtml::encode($data->tilte), array('view', 'id'=>$data->id)); ?></td>
    <td><?php echo CHtml::encode($data->gameName->gname); ?></td>
    <td><?php echo CHtml::encode($data->articleType->typename); ?></td>
    <td><?php echo CHtml::encode($data->keywords); ?></td>
    <td><?php echo CHtml::encode($data->description); ?></td>
    <td><a href=javascript:void(0) class="imgUrl" title='<?php echo CHtml::encode($data->imgurl); ?>'>查看缩略图</a></td>
  </tr>
