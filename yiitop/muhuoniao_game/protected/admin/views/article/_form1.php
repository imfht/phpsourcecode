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
    <td><?php echo $form->dropDownList($model,'tid', ArticleType::model()->getArticleType(), array('class'=>'select')); ?></td>
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
    <td><?php echo CHtml::activeFileField($model,'imgurl'); ?><p>
    <a href="#">显示缩略图</a>
    </p>
    
    
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
				//'imagePath'=>'/attachment/ueditor/',
				/*'toolbars' =>array(
					"customstyle","paragraph","fontfamily","fontsize","forecolor","Underline","bold","italic","strikethrough","BackColor","|",
				"rowspacingtop","rowspacingbottom","lineheight","|","superscript","subscript","|","JustifyCenter","Justifyleft","JustifyRight","justifyjustify",
				"|","directionalityltr","directionalityrtl","indent","removeformat","formatmatch","autotypeset","pasteplain","|","insertunorderedlist",
				"insertorderedlist","|","blockquote","link","unlink","highlightcode","|","undo","redo","source","|","InsertImage","ImageNone","ImageLeft",
				"ImageCenter","ImageRight","wordimage","|","cleardoc","selectall","print","searchreplace","preview","help","|","gmap","map","webapp","|","pagebreak",
				"music","scrawl","attachment","snapscreen","emotion","insertvideo","insertframe","template","background","date","time",
				"horizontal","anchor","spechars","sourceEditor","contextMenu","autoHeightEnabled","|","inserttable","deletetable","|","mergeright",
				"mergedown","|","splittorows","splittocols","|","splittocells","mergecells","|","insertcol","insertrow","|","deletecol","deleterow","|"
				),*/
			)); 
 
		?></td>
 
  </tr><!--

   <tr>
    <th>发布时间：</th>
    <td><input type="text" value="2013-04-28 14:00" class="text" /></td>
  </tr>

     --><tr>
    <th>是否发布：</th>
    <td><?php echo $form->dropDownList($model,'display',Yii::app()->params['display'],array('class'=>'select')); ?></td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <td><?php echo CHtml::submitButton($model->isNewRecord ? '添加文章' : '确认修改'); ?></td>
  </tr>
</table>


<?php $this->endWidget(); ?>