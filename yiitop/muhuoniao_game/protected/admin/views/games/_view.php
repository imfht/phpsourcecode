<tr class="ood">
    <td><?php echo CHtml::encode($data->id); ?></td>
    <td><?php echo CHtml::link(CHtml::encode($data->gname), array('view', 'id'=>$data->id)); ?></td>
    <td><?php 
		if($data->server_id!=''){
		  $server=unserialize($data->server_id); 
		  echo CHtml::dropDownList('server_id', $model, $server);
		}else{
			echo '尚未添加分区';
		}
	?></td>
    <td><?php echo date("Y年m月d日",$data->create_time); ?></td>
    <td><?php $display=Yii::app()->params['display'];echo $display[$data->display]; ?></td>
    </tr>