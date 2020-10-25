<?php
/* @var $this DailyreportController */
/* @var $model Dailyreport */
/* @var $form CActiveForm */
?>

<script type="text/javascript">
		function read(){
			var maxl=50;
			var content=document.getElementById("con").value;
			var relcon=content.split(/[\t*\s*]/g);
			var s=relcon.join("").length;
			//var s=Array.join(content.split(/[\t*\s*]/g),"").length;
			//var s=document.getElementById("con").value.length;
			if(maxl>s)
				document.getElementById("num").innerHTML="已输入"+s+"/"+maxl+" 字符，还需要至少"+(maxl-s)+"个字。（注意：空格、回车不算字数。）";
			else
				document.getElementById("num").innerHTML="已输入"+s+"个字符，已满足要求！";
		}
</script>
<body onload="read()">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dailyreport-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
)); ?>
	
	<div class="row">
		<?php echo $form->textArea($model,'content',array('rows'=>8,'cols'=>20,'id'=>'con','style'=>"height:150px;width:450px;",'onkeyup'=>'read()')); ?>
		<div><span id="num" style="color:purple">已输入0个字符</span></div>
		<?php echo $form->error($model,'content'); ?>
	</div>
	<?php if(Yii::app()->user->hasFlash('warning')):?>
	<div id="flash-warning">
			<?php $this->widget('bootstrap.widgets.TbAlert', array(
				'block'=>true, // display a larger alert block?
				'fade'=>true, // use transitions?
				'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
				'alerts'=>array( // configurations per alert type
					'warning'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
				),
		)); ?>
	</div>
	<?php endif;?>
	<div class="row buttons" style="text-align:center">
	<?php $this->widget('bootstrap.widgets.TbButton',array(
		'buttonType'=>'submit',
		'type'=>$model->isNewRecord? 'success':'info',
		'size'=>'large',
		'icon'=>'icon-ok',
		'label'=>$model->isNewRecord? '发布':'保存',
	));?>
	</div>
<?php $this->endWidget(); ?>
</div>
</body>