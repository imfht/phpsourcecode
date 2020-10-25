<?php defined('APP_PATH') OR exit('不允许访问'); ?>
<div class="row" >
	<div class="col-lg-12">
		<h4>位置：<?php echo (isset($editor_type) && $editor_type)?'新增图文':'图文编辑';?> </h4>
	</div>
</div>
<div class="row">
	<div class="col-lg-10 col-lg-offset-1 col-md-12 col-sm-12 col-xs-12">
		{$error_msg??''}
		<form id="editor_form" action="{$public}news/index/{$editor_type?'add':'editor'}" class="form-horizontal" role="form" method="post">
			{$ecms_hashur['form']??''}
			<input type="hidden" name="id" value="<?php echo  isset($news['id'])?$news['id']:(isset($form['id'])?$form['id']:'');?>"/>
			<input type="hidden" name="operation_form" id="operation_form" value="{$form.operation_form??''}"/>
			<input type="hidden" name="editor_type" id="editor_type" 
				value="<?php echo  isset($editor_type)?$editor_type:((isset($form['editor_type']))?$form['editor_type']:'');?>"/>
			<div class="form-group">
				<label for="title" class="control-label col-lg-1 col-xs-2">标题：</label>
				<div class="col-xs-5">
					<input type="text" class="form-control" name="title" id="title" onkeyup="maxText(this.id,'title_maxText')" size="45" maxlength="128" 
						value="<?php echo isset($news['title'])?$news['title']:(isset($form['title'])?$form['title']:'');?>" placeholder="标题，请注意字数限制"/>
				</div>
				<label id="title_maxText" for="title" class="control-label col-xs-2" style="max-width:50px;">0/64</label>
				{$form_error.title??''}
			</div>
			<div class="form-group">
				<label for="author" class="control-label col-lg-1 col-xs-2">作者：</label>
				<div class="col-xs-5">
					<input type="text" class="form-control" name="author" id="author" onkeyup="maxText(this.id,'author_maxText')" size="45" maxlength="16" 
						value="<?php echo isset($news['author'])?$news['author']:(isset($form['author'])?$form['author']:'');?>" placeholder="作者，请注意字数限制"/>
				</div>
				<label id="author_maxText" for="author" class="control-label col-xs-2" style="max-width:50px;">0/8</label>
				{$form_error.author??''}
			</div>
			<div class="form-group">
				<label for="abstract" class="control-label col-lg-1 col-xs-2">摘要：</label>
				<div class="col-xs-5">
					<textarea name="abstract" rows="4" id="abstract" class="form-control" placeholder="选填，该摘要有可能不会完整显示，例如：作为多图文成员发送时；转发时；等等。（这是由微信端显示机制控制的）" maxlength="240" 
						onkeyup="maxText(this.id,'abstract_maxText')" style="max-width: 100%;resize: none;"><?php echo isset($news['abstract'])?$news['abstract']:(isset($form['abstract'])?$form['abstract']:'');?></textarea>
				</div>
				<label id="abstract_maxText" class="control-label col-xs-2" style="max-width:50px;">0/120</label>
				{$form_error.abstract??''}	
			</div>
			<div class="form-group">
				<label for="send_time" class="control-label col-lg-1 col-xs-2">发布时间：</label>
				<div class="col-xs-5">
					<input type="datetime-local" class="form-control" name="send_time" id="send_time" 
						value="<?php echo (isset($news['send_time']) && !empty($news['send_time']))?date('Y-m-d\\TH:i',strtotime($news['send_time'])):'';?>" 
						placeholder="输入正式发布时间，此时间前为关闭状态"/>
				</div>
				<label for="send_time" class="control-label col-xs-2 btn btn-info" style="max-width: 50px;text-align:center" 
					onclick="editorTime('send_time');">更新</label>
				{$form_error.send_time??''}
			</div>
			<div class="form-group">
				<label for="title_img" class="control-label col-lg-1 col-xs-2">封面图片<br><a href="javascript:void(0)" data-toggle="tooltip" title="作为单图文或多图文的主图文，建议尺寸：900px*500px；否则建议长宽比为1:1">建议</a></label>
				<div class="col-xs-5">
					<input type="hidden" name="title_img" size="30" id="title_img" value="<?php echo isset($news['title_img'])?$news['title_img']:(isset($form['title_img'])?$form['title_img']:'');?>"/>
					<div id="title_img_border" class="col-lg-4 col-xs-6" style="<?php echo (isset($news['title_img']) || (isset($form['title_img']) && $form['title_img']))?'':'display:none';?>">
						<img class="img-responsive" src="<?php echo  isset($news['title_img'])?$news['title_img']:(isset($form['title_img'])?$form['title_img']:'')?>" alt="封面图片展示" width="162" height="90" id="title_img_view"/>&nbsp;
					</div>
					<div class="col-lg-6 col-xs-6">
						<span onclick="img_delete_for_news('title_img');" class="btn btn-danger">删除</span>
						<span onclick="upImage('title_img');" class="btn btn-warning">选择封面图片</span>
					</div>
				</div>
				<div class="col-xs-2" style="max-width: 50px"></div>
				{$form_error.title_img??''}
			</div>
			<div class="form-group">
				<label for="is_link_img" class="control-label col-lg-1 col-xs-2">显示在正文:</label>
				<div class="col-xs-5">
					<label class="checkbox-inline"><input type="radio" name="is_link_img" value="1" 
						<?php echo ((isset($news['is_link_img']) && $news['is_link_img']==1) || (isset($form['is_link_img']) && $form['is_link_img']==1))?"checked='checked'":"";?>>是</label>
					<label class="checkbox-inline"><input type="radio" name="is_link_img" value="0" 
						<?php echo ((isset($news['is_link_img']) && $news['is_link_img']==1) || (isset($form['is_link_img']) && $form['is_link_img']==1))?'':"checked='checked'";?>/>否</label>
				</div>
				<div class="col-xs-2" style="max-width: 50px"></div>
				{$form_error.is_link_img??''}
			</div>
			<div class="form-group">
				<label for="url" class="control-label col-lg-1 col-xs-2">原文链接</label>
				<div class="col-xs-5">
					<input type="url" name="url" id="url" class="form-control" value="<?php echo isset($news['url'])?$news['url']:(isset($form['url'])?$form['url']:'');?>" 
						placeholder="可空，请输入完整链接，包括http://"/>
				</div>
				<div class="col-xs-2" style="max-width: 50px"></div>
				{$form_error.url??''}
			</div>
			<div class="form-group">
				<label for="outside_url" class="control-label col-lg-1 col-xs-2">开启外链：</label>
				<div class="col-xs-5">
					<input type="text" class="form-control" name="outside_url" id="outside_url" 
						<?php echo ((isset($news['is_open_outside']) && $news['is_open_outside']==1) || (isset($form['is_open_outside']) && $form['is_open_outside']==1))?'':'disabled="disabled"';?> 
						value="<?=isset($news['outside_url'])?$news['outside_url']:(isset($form['outside_url'])?$form['outside_url']:'');?>" placeholder="开启则以下内容失效，但已录内容将被保存">
				</div>
				<label for="outside_url" class="control-label col-xs-1 btn btn-info" style="max-width: 50px;text-align:center" onclick="switchById(this,'is_open_outside')">
					<?=((isset($news['is_open_outside']) && $news['is_open_outside']==1) || (isset($form['is_open_outside']) && $form['is_open_outside']==1))?'关闭':'开启';?>
				</label>
				<input type="hidden" name="is_open_outside" id="is_open_outside" value="<?=((isset($news['is_open_outside']) && $news['is_open_outside']==1) || (isset($form['is_open_outside']) && $form['is_open_outside']))?'1':'0';?>"/>
				{$form_error.outside_url??''}
			</div>
			<div id="news_location_border" <?=((isset($news['is_open_outside']) && $news['is_open_outside']==1) || (isset($form['is_open_outside']) && $form['is_open_outside']==1))?'style="display:none"':'';?>>
				{/*百度编辑器的宽度暂不能自适应，小屏幕时，会挡住右侧……故将验证信息单独起一行*/}
				
				<div class="form-group">
					<div class="row">
						<div class="col-lg-1 col-xs-2"></div>
						<div class="col-xs-5"></div>
						<div class="col-xs-2" style="max-width: 50px"></div>
						{$form_error.content??''}
					</div>
					<label for="editor" class="control-label col-lg-1 col-xs-2">正文：</label>
					<div class="col-xs-5">
						{/*下面的<script></script>以及之间的内容必须写在同一行，否者“换行符”，在编辑器载入时自动转换为段落标记<p></p>*/}
						<script id="editor" type="text/plain" name="content" 
							><?=isset($news['content'])?$news['content']:(isset($form['content'])?$form['content']:'');?></script>
					</div>
				</div>
				
			</div>
			<div class="form-group">
				<div class="col-xs-5 col-xs-offset-2 col-lg-offset-1">
					<input type="button" class="form-control btn btn-success" value="提交" 
						onClick="editorInput('<?=isset($news)?"refresh":(isset($form['editor_type'])?$form['editor_type']:"addNews");?>','editor_type');editorInput('editor_form','operation_form');editorModal('确定提交吗？');" 
						data-toggle="modal" data-target="#myModal"/>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/plain" id="j_ueditorupload"></script>
<script>
function isView(e,id){
	if($(e).is(':checked')){
		$('#'+id).attr("disabled",false); ;
	}else{
		$('#'+id).attr("disabled",true); ;
	}		
}
function isViewCover(e,id){
	if($(e).is(':checked')){
		$('#'+id).hide(); ;
	}else{
		$('#'+id).show(); ;
	}		
}
function switchById(obj,id){
	var e=$('#'+id);
	var v=e.val();
	if(v==0){
		e.val('1');
		obj.innerHTML='关闭';
		$('#outside_url').attr("disabled",false);
		$('#news_location_border').hide();
	}else{
		e.val('0');
		obj.innerHTML='开启';
		$('#outside_url').attr("disabled",true);
		$('#news_location_border').show();
	}
}
//使用UE做图片独立上传
//实例化编辑器
//var URL = window.UEDITOR_HOME_URL || getUEBasePath();
var ue = UE.getEditor('j_ueditorupload');
var text = UE.getEditor('editor',{initialFrameHeight:400,initialFrameWidth:545,allowDivTransToP: false,imagePopup:true});
setTimeout(function () {
	ue.execCommand('drafts');
}, 500); //注意一定要延时。要等这玩意载入成功。
text.ready(function (){
	
	text.execCommand('serverparam',{
		'classid' : '{$lur.classid??""}',
		'filepass': '{$lur.filepass??""}',
		'userid'  : '<?=isset($isadmin)?(isset($logininid)?$logininid:''):(isset($lur['userid'])?$lur['userid']:'');?>',
		'username': '<?=isset($isadmin)?(isset($loginin)?$loginin:''):(isset($lur['username'])?$lur['username']:'')?>',
		'rnd'     : '<?=isset($isadmin)?(isset($loginrnd)?$loginrnd:''):(isset($lur['rnd'])?$lur['rnd']:'')?>'
    });
});
ue.ready(function (){
	ue.setHide();
	ue.execCommand('serverparam',{
		'classid' : '{$lur.classid??""}',
		'filepass': '{$lur.filepass??""}',
		'userid'  : '<?=isset($isadmin)?(isset($logininid)?$logininid:''):(isset($lur['userid'])?$lur['userid']:'');?>',
		'username': '<?=isset($isadmin)?(isset($loginin)?$loginin:''):(isset($lur['username'])?$lur['username']:'')?>',
		'rnd'     : '<?=isset($isadmin)?(isset($loginrnd)?$loginrnd:''):(isset($lur['rnd'])?$lur['rnd']:'')?>'
    });
});
//弹出图片上传的对话框
function upImage(id_name){
	var myImage = ue.getDialog("insertimage");
	myImage.open();
	//监听图片上传
	ue.addListener('beforeInsertImage', function (t,arg){
		//alert('这是图片地址：'+arg[0].src);
		var id_view = id_name + "_view";
		var id_td = id_name + '_border';
		var view = document.getElementById(id_view);
		var border = document.getElementById(id_td);
		document.getElementById(id_name).value = arg[0].src;
		view.src = arg[0].src;
		border.style.display = '';
	});
}
$(document).ready(function(){
	maxText('title','title_maxText');
	maxText('author','author_maxText');
	maxText('abstract','abstract_maxText');
});

</script>