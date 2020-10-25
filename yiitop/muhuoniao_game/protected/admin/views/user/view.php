<!---------------side_right---------------->

<div id="side_right">
<h2><strong>用户管理</strong></h2>
<h3><span class="title">用户<strong><?php echo $model->username;?></strong>信息</span></h3>

<div class="create">

<form>
<table width="900" border="0" class="table_b">
 <tr>
    <th>用户ID：</th>
    <td><?php echo $model->id;?></td>
  </tr>
  <tr>
    <th>用户名：</th>
    <td><?php echo $model->username;?></td>
  </tr>
  <tr>
    <th>管理权限：</th>
    <td><?php $status=$model->status;echo Yii::app()->params['role'][$status]?></td>
  </tr>
    <tr>
    <th>创建时间：</th>
    <td><?php echo date('Y-m-d H:i:s',$model->cteate_time);?></td>
  </tr>
  <tr>
    <th>上次登陆时间：</th>
    <td><?php echo date('Y-m-d H:i:s',$model->login_time);?></td>
  </tr>
  <tr>
    <th>登陆IP：</th>
    <td><?php echo $model->ip;?></td>
  </tr>
  <tr>
    <th>编辑：</th>
    <td><div class="update"><img src="<?php echo Yii::app()->baseUrl;?>/system/images/update.png" /><span><?php echo CHtml::link('修改信息',array('update','id'=>$model->id))?></span><img src="<?php echo Yii::app()->baseUrl;?>/system/images/delete.png" /><span><?php echo CHtml::link('删除用户',array('delete','id'=>$model->id),array('confirm'=>'确认删除此用户？'))?></span></div></td>
  </tr>
</table>


</form>

</div>

</div>
<!---------------side_right end---------------->

