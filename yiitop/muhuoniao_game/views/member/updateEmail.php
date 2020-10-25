<script src="<?php echo Yii::app()->request->baseUrl?>/js/change/jquery.mailAutoComplete.js"></script>
<script>
$(function(){
	$("#111Member_email").mailAutoComplete({
	    boxClass: "out_box", //外部box样式
	    listClass: "list_box", //默认的列表样式
	    focusClass: "focus_box", //列表选样式中
	    markCalss: "mark_box", //高亮样式
	    autoClass: false,
	    textHint: true, //提示文字自动隐藏
	    hintText: "请输入邮箱地址"
	});
})
</script>
<div id="main_right">
		<div class="email"> 
		<h2>修改邮箱</h2>
	<?php $form=$this->beginWidget("CActiveForm",array(
		'id'=>'member-form',
		'enableAjaxValidation'=>true,
		'enableClientValidation'=>true,	
		'clientOptions'=>array('validateOnSubmit'=>true),
	));?> 
	
	<p><?php echo $form->textField($model,'email',array('value'=>'','size'=>25,'autocomplete'=>'off'));?><input type="submit" value="确认修改" style="padding:0 10px; margin-left:30px;" /></p>
	
	<?php echo $form->error($model,'email');?>
	
	<?php $this->endWidget();?> 
	</div>
</div>