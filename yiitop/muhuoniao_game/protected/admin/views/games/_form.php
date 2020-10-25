<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'games-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>
<script type="text/javascript" src="<?php echo Yii::app()->baseUrl;?>/system/js/jquery.setImagePreview.js"></script>
<script type="text/javascript">
$(function(){
	$("#Games_logo").change(function(){
		setImagePreview('Games_logo','previewLogo','localLogo',200,300);
	});
	$("#Games_imgurl").change(function(){
		setImagePreview('Games_imgurl','previewImage','localImage',200,300);
	});
	$(".delete_server").live('click',function(){
		$(this).parent(".server_id").remove();
	});
	$(".add_server").bind('click',function(){
		var $p = '<p class=server_id><?php echo $form->textField($model,'server_id[]',array('size'=>30,'maxlength'=>255,'class'=>'text')); ?><a class=delete_server href=javascript:void(0)></a></p>';
		$(this).before($p);
	});
})

</script>
<table width="900" border="0" class="table_b">
  <tr>
    <th>游戏名称：</th>
    <td><?php echo $form->textField($model,'gname',array('size'=>20,'maxlength'=>20,'class'=>'text')); ?></td>
  </tr>
  <tr>
    <th>游戏别名：</th>
    <td><?php echo $form->textField($model,'alias',array('size'=>20,'maxlength'=>20,'class'=>'text')); ?></td>
  </tr>
    <tr>
    <th>大区：</th>
    <td><?php 
		if($model->server_id){
			if($model->server_id=unserialize($model->server_id)){
				//var_dump($model->server_id);
				//exit;
				foreach ($model->server_id as $server_id){
					echo '<p class=server_id>';
					echo $form->textField($model,'server_id[]',array('size'=>30,'maxlength'=>255,'value'=>$server_id,'class'=>'text')); 
					echo '<a class=delete_server href=javascript:void(0)></a>';
					echo '</p>';
				}
			}
			
		}else{
			echo '<p class=server_id>';
			echo $form->textField($model,'server_id[]',array('size'=>30,'maxlength'=>255,'class'=>'text'));
			echo '<a class=delete_server href=javascript:void(0)></a>';
			echo '</p>';
		}
		?><p><a href="javascript:void(0)" class="add_server">增加</a></p>
    </td>
  </tr>
   <tr>
    <th>游戏LOGO：</th>
    <td><?php echo CHtml::activeFileField($model,'logo',array('class'=>'file')); ?>
    <p><a href="#">图片预览</a></p>
    	<?php if(!$model->isNewRecord){?>
     		<p id="localLogo" style="padding:8px;width:200px;height:300px;border:1px solid #ccc;"><img id="previewLogo" style="padding:0;margin:0;border:none;"  alt="" src="http://918s-game.stor.sinaapp.com/<?php echo $model->logo;?>" width="200" height="300"  /></p>
		 	<input type="hidden" name="LogoImgPath" id="hiddenFieldLogo"    value="<?php echo $model->logo;?>"/>
		<?php }?>
    	<?php if($model->isNewRecord):?>
	    <p id="localLogo" style="padding:8px;width:200px;height:300px;border:1px solid #ccc;"><img id="previewLogo" width=-1 height=-1 style="diplay:none;border:none;" /></p>
	    <?php endif;?>
    </td>
  </tr>
   <tr>
    <th>游戏缩略图：</th>
    <td><?php echo CHtml::activeFileField($model,'imgurl',array('class'=>'file')); ?>
    <p><a href="#">图片预览</a></p>
    	<?php if(!$model->isNewRecord){?>
     		<p id="localImage" style="padding:8px;width:200px;height:300px;border:1px solid #ccc;"><img id="previewImage" style="padding:0;margin:0;border:none;"  alt="" src="http://918s-game.stor.sinaapp.com/<?php echo $model->imgurl;?>" width="200" height="300"  /></p>
		 	<input type="hidden" name="ImgPath" id="hiddenFieldImgurl"    value="<?php echo $model->imgurl;?>"/>
		<?php }?>
    	<?php if($model->isNewRecord):?>
	    <p id="localImage" style="padding:8px;width:200px;height:300px;border:1px solid #ccc;"><img id="previewImage" width=-1 height=-1 style="diplay:none;border:none;" /></p>
	    <?php endif;?>
    </td>
  </tr>
  <tr>
    <th>是否发布：</th>
    <td><?php echo $form->dropDownList($model,'display',Yii::app()->params['display'],array('class'=>'select')); ?></td>
  </tr>
  <tr>
    <th>游戏属性：</th>
    <td><?php echo $form->dropDownList($model,'flag',Yii::app()->params['flag'],array('class'=>'select')); ?></td>
  </tr>
  <tr>
    <th>游戏类别：</th>
    <td><?php echo $form->dropDownList($model,'type',Yii::app()->params['gametype'],array('class'=>'select')); ?></td>
  </tr>
  <tr>
    <th>&nbsp;</th>
   <td><?php echo CHtml::submitButton($model->isNewRecord ? '添加游戏' : '确认修改'); ?></td>
  </tr>
</table>
<?php $this->endWidget(); ?>
