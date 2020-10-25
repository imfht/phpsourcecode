
<!---------------side_right---------------->

<div id="side_right">
<h2><strong>文章管理</strong></h2>
<h3><span class="title">修改文章<strong><?php echo $model->tilte;?></strong>信息</span></h3>

<div class="create"><!--
'id',
		'tilte',
		'gid',
		'tid',
		'keywords',
		'description',
		'imgurl',
		'content',
		'create_time',
		'create_author_id',
		'up_time',
		'up_author_id',
		'display',
--><form>
<table width="900" class="table_b">
 <tr>														
    <th>文章ID：</th>
    <td><?php echo $model->id;?></td>
  </tr>
  <tr>
    <th>文章标题：</th>
    <td><?php echo $model->tilte;?></td>
  </tr>
    <tr>
    <th>游戏：</th>
    <td><?php echo $model->gameName->gname;?></td>
  </tr>
  <tr>
    <th>栏目：</th>
    <td><?php echo $model->articleType->typename;?></td>
  </tr>
  <tr>
    <th>关键字：</th>
    <td><?php echo $model->keywords ?  $model->keywords :  '没有添加关键字';?></td>
  </tr>
    <tr>
    <th>描述：</th>
    <td><?php echo $model->description?$model->description:'没有添加描述';?></td>
  </tr>
  <tr>
    <th>缩略图：</th>
    <td>
    
    <P>
    
    <img src="http://918s-upload.stor.sinaapp.com/<?php echo $model->imgurl;?>" width="200" height="100"/>
    
    </P>
    
    
    </td>
  </tr>
   <tr>
    <th>文章内容：</th>
    <td><?php echo CHtml::encode($model->content);?></td>
  </tr>
  <tr>
    <th>创建时间：</th>
    <td><?php echo date('Y-m-d H:i:s',$model->create_time);?></td>
  </tr>
  <tr>
    <th>创建作者：</th>
    <td>admin</td>
  </tr>
   <tr>
    <th>修改时间：</th>
    <td><?php echo date('Y-m-d H:i:s',$model->up_time);?></td>
  </tr>
  <tr>
    <th>发布时间：</th>
    <td><?php echo date('Y-m-d H:i:s',$model->sort_time);?></td>
  </tr>
     <tr>
    <th>是否发布：</th>
    <td><?php echo Article::model()->getArticleDisplay($model->display);?></td>
  </tr>
     <tr>
    <th>编辑：</th>
    <td><div class="update"><img src="<?php echo Yii::app()->baseUrl;?>/system/images/update.png" /><span><?php echo CHtml::link('修改文章',array('update','id'=>$model->id))?></span><img src="<?php echo Yii::app()->baseUrl;?>/system/images/delete.png" /><span><?php echo CHtml::link('删除文章',array('delete','id'=>$model->id),array('confirm'=>'确认删除此数据？'))?></span></div></td>
  </tr>
</table>


</form>

</div>






</div>
<!---------------side_right end---------------->