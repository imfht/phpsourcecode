<div id="side_right">
<h2><strong>游戏管理</strong></h2>
<h3><span class="title">游戏<strong><?php echo  $model->gname; ?></strong>信息</span></h3>

<div class="create">

<form>
<table width="900" border="0" class="table_b">
 <tr>
    <th>游戏ID：</th>
    <td><?php echo $model->id; ?></td>
  </tr>
  <tr>
    <th>游戏名称：</th>
    <td>
    <?php echo  $model->gname; ?>
   </td>
  </tr>
  <tr>
    <th>游戏别名：</th>
    <td><?php echo  $model->alias; ?></td>
  </tr>
    <tr>
    <th>大区：</th>
    <td><?php
		if($model->server_id){
		  $server=unserialize($model->server_id); 
		  foreach($server as $value){
			echo $value."区,";
		  }
		}else{
			echo '尚未添加大区';
		}
?>
    </td>
  </tr>
   <tr>
    <th>游戏logo:</th>
    <td>
    <?php if($model->logo==''):?>
    <p><img src="<?php echo Yii::app()->request->baseUrl;?>/system/images/game_01.jpg" /></p>
    <?php else:?>
    <p id="localLogo" style="padding:8px;width:200px;height:300px;border:1px solid #ccc;"><img id="preview_Logo" style="padding:0;margin:0;border:none;"  alt="" src="http://918s-game.stor.sinaapp.com/<?php echo $model->logo;?>" width="200" height="300"  /></p>
    <?php endif;?>
    </td>
  </tr>
   <tr>
    <th>游戏缩略图：</th>
    <td>
    <?php if($model->imgurl==''):?>
    <p><img src="<?php echo Yii::app()->request->baseUrl;?>/system/images/game_01.jpg" /></p>
    <?php else:?>
    <p id="localImage" style="padding:8px;width:200px;height:300px;border:1px solid #ccc;"><img id="preview_Image" style="padding:0;margin:0;border:none;"  alt="" src="http://918s-game.stor.sinaapp.com/<?php echo $model->imgurl;?>" width="200" height="300"  /></p>
    <?php endif;?>
    </td>
  </tr>
  <tr>
    <th>是否发布：</th>
    <td>
<?php $display=Yii::app()->params['display']; echo  $display[$model->display]; ?>
</td>
  </tr>
  <tr>
    <th>游戏属性：</th>
    <td>
<?php $flag=$model->flag; echo Yii::app()->params['flag'][$flag]; ?>
</td>
  </tr>
  <tr>
    <th>游戏类别：</th>
    <td>
<?php $gametype=Yii::app()->params['gametype']; echo $gametype[$model->type]; ?></td>
  </tr>
   <tr>
    <th>编辑：</th>
   <td><div class="update"><img src="<?php echo Yii::app()->baseUrl;?>/system/images/update.png" /><span><?php echo CHtml::link('修改信息',array('update','id'=>$model->id))?></span><img src="<?php echo Yii::app()->baseUrl;?>/system/images/delete.png" /><span><?php echo CHtml::link('删除用户',array('delete','id'=>$model->id),array('confirm'=>'确认删除此用户？'))?></span></div></td>
  </tr>
</table>


</form>

</div>
</div>