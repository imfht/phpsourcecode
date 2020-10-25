<tr class="ood">
    <td><?php echo CHtml::encode($data->id); ?></td>
    <td><?php echo CHtml::link(CHtml::encode($data->typename), array('view', 'id'=>$data->id)); ?></td>
    <td><?php echo ArticleType::model()->getParentArticleType(CHtml::encode($data->tid)); ?></td>
    <td><?php echo CHtml::encode($data->create_author->username); ?></td>
    <td><?php echo CHtml::encode($data->up_author->username); ?></td>
    <td><?php echo CHtml::encode(date('Y-m-d H:i:s',$data->create_time)); ?></td>
    <td><?php echo CHtml::encode(date('Y-m-d H:i:s',$data->up_time)); ?></td>
  </tr>
