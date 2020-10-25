<!---------------side_right---------------->

<div id="side_right">
<h2><strong>游戏API管理</strong></h2>
<h3><span class="title">游戏API<strong><?php echo $model->gameName->gname;?></strong>信息</span></h3>

<div class="create">

<table width="900" border="0" class="table_b">
 <tr>
    <th>游戏Api ID：</th>
    <td><?php echo $model->id; ?></td>
  </tr>
  <tr>
    <th>游戏名称：</th>
    <td><?php echo $model->gameName->gname;?></td>
  </tr>
  <tr>
    <th>平台服务器的注册用户编号：</th>
    <td><?php echo $model->userid;?></td>
  </tr>
  <tr>
    <th>平台服务器的通行证帐号：</th>
    <td><?php echo $model->username;?></td>
  </tr>
    <tr>
    <th>密钥：</th>
    <td><?php echo $model->password;?></td>
  </tr>
   <tr>
    <th>编辑：</th>
   <td><div class="update"><img src="<?php echo Yii::app()->baseUrl;?>/system/images/update.png" /><span><?php echo CHtml::link('修改信息',array('update','id'=>$model->id))?></span><img src="<?php echo Yii::app()->baseUrl;?>/system/images/delete.png" /><span><?php echo CHtml::link('删除API',array('delete','id'=>$model->id),array('confirm'=>'确认删除数据？'))?></span></div></td>
  </tr>
</table>

</div>






</div>
<!---------------side_right end---------------->