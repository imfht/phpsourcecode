<script type="text/javascript" src="<?php echo Yii::app()->baseUrl;?>/system/js/jquery.setImagePreview.js"></script>
<script type="text/javascript">
<!--
$(function(){
	$("#Article_imgurl").change(function(){
		setImagePreview('Article_imgurl','preview_1','localImag_1',200,300);
	});
})
//-->
</script>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'article-form',
	'enableAjaxValidation'=>true,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->error($model,'tilte'); ?>
<?php echo $form->error($model,'gid'); ?>
<?php echo $form->error($model,'tid'); ?>
<?php echo $form->error($model,'keywords'); ?>
<?php echo $form->error($model,'description'); ?>
<?php echo $form->error($model,'imgurl'); ?>
<?php echo $form->error($model,'content'); ?>
<?php echo $form->error($model,'display'); ?>
<table width="900" border="0"  class="table_b">
  <tr>
    <th>文章标题：</th>
    <td><?php echo $form->textField($model,'tilte',array('class'=>'text','maxlength'=>255)); ?></td>
  </tr>
    <tr>
    <th>游戏：</th>
    <td><?php echo $form->dropDownList($model, 'gid',  Games::model()->getGamesAllShow(), array('class'=>'select'));?></td>
  </tr>
  <tr>
    <th>栏目：</th>
     <td><?php echo $form->dropDownList($model, 'tid',  CHtml::listData(ArticleType::model()->findAll(), 'id', 'typename'), array('class'=>'select'));?></td>
  </tr>
  <tr>
    <th>关键字：</th>
    <td><?php echo $form->textField($model,'keywords',array('size'=>50,'maxlength'=>50,'class'=>'text')); ?></td>
  </tr>
    <tr>
    <th>描述：</th>
    <td><?php echo $form->textArea($model,'description',array('class'=>'textarea')); ?> </td>
  </tr>
  <tr>
    <th>缩略图：</th>
    <td><?php echo CHtml::activeFileField($model,'imgurl'); ?>
     	<?php if(!$model->isNewRecord){?>
     		<p id="localImag_1" style="padding:8px;width:200px;height:300px;border:1px solid #ccc;"><img id="preview_1" style="padding:0;margin:0;border:none;"  alt="" src="http://918s-upload.stor.sinaapp.com/<?php echo $model->imgurl;?>" width="200" height="300"  /></p>
		 	<input type="hidden" name="imgpath2" id="hiddenField"    value="<?php echo $model->imgurl;?>"/>
		<?php }?>
	    <?php if($model->isNewRecord):?>
	    <p id="localImag_1" style="padding:8px;width:200px;height:300px;border:1px solid #ccc;"><img id="preview_1" width=-1 height=-1 style="diplay:none;border:none;" /></p>
	    <?php endif;?>
    </td>
  </tr>
   <tr>
    <th>文章内容：</th>
    <td><?php
			$this->widget('ext.wdueditor.WDueditor',array(
				'model' => $model,
				'attribute' => 'content',
				'language' =>'zh-cn',
				'width' =>'100%',  
				'height' =>'600',
			)); 
 
		?></td>
 
  </tr>
	<tr>
	<th>发布时间：</th>
	 <td><?php echo $form->textField($model,'sort_time',array('size'=>40,'class'=>'text','maxlength'=>150,'value'=>$model->sort_time?date('Y-m-d H:i:s',$model->sort_time):date('Y-m-d H:i:s',time()))); ?>
	  请按照2013-05-21 14:21:13这种格式填写默认是当前时间</td>
  </tr> 
  <tr>
    <th>是否发布：</th>
    <td><?php echo $form->dropDownList($model,'display',Yii::app()->params['display'],array('class'=>'select')); ?></td>
	 </tr>
  <tr>
    <th>&nbsp;</th>
    <td><?php echo CHtml::submitButton($model->isNewRecord ? '添加文章' : '确认修改'); ?></td>
  </tr>
</table>


<?php $this->endWidget(); ?>