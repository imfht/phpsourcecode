<div id="side_right">
<h2><strong>栏目管理</strong></h2>
<h3><span class="title">栏目<strong><?php echo  $model->typename; ?></strong>信息</span></h3>

<div class="create">

<form>
<table width="900" border="0" class="table_b">
 <tr>
    <th>文章类型ID：</th>
    <td><?php echo $model->id; ?></td>
  </tr>
  <tr>
    <th>父级栏目</th>
    <td><?php echo ArticleType::model()->getParentArticleType( $model->tid); ?></td>
  </tr>
  <tr>
    <th>栏目名称：</th>
    <td><?php echo  $model->typename; ?></td>
  </tr>
  <tr>
    <th>创建用户：</th>
    <td><?php echo User::model()->getAuthorName( $model->create_author_id); ?></td>
  </tr>
  <tr>
    <th>更新用户：</th>
    <td><?php echo User::model()->getAuthorName( $model->up_author_id); ?></td>
  </tr>
  <tr>
    <th>创建时间：</th>
    <td><?php echo date('Y-m-d H:i:s', $model->create_time); ?></td>
  </tr>
  <tr>
    <th>更新时间：</th>
    <td><?php echo date('Y-m-d H:i:s', $model->up_time); ?></td>
  </tr>
    <tr>
    <th>编辑：</th>
    <td><div class="update"><img src="<?php echo Yii::app()->baseUrl;?>/system/images/update.png" /><span><?php echo CHtml::link('修改信息',array('update','id'=>$model->id))?></span><img src="<?php echo Yii::app()->baseUrl;?>/system/images/delete.png" /><span><?php echo CHtml::link('删除用户',array('delete','id'=>$model->id),array('confirm'=>'确认删除此用户？'))?></span></div></td>
  </tr>
</table>
</form>
</div>
</div>
