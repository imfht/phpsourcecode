<?php defined('APP_PATH') OR exit('No direct script access allowed'); ?>

<div class="row" >
	<div class="col-lg-12">
		<h4>位置：{$title??''}</h4>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<form action="{$public}file/index/index" id=list_form method="post">
			{$error_msg??''}
			{$ecms_hashur['form']??''}
			<input name="per_page" id="tpage" type="hidden">
			<table class="table table-bordered table-hover text-left"> 
				<tr>
					<td>&nbsp;<span>附件筛选</span></td>
					<td>
						<div class="form-inline">
							<div class="form-group">
								<input name="search" type="search" class="form-control" value="{$search??''}" placeholder="请输入搜索内容">
							</div>
							<div class="form-group">
								<input name="dosearch" type="submit" value="搜索" class="btn btn-warning">&nbsp;
									<?php echo (!isset($search) || (empty($search) && $search !==0))?'':'正在搜索：'.$search;?>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<span >附件类型</span>
						<input type="hidden" name="type" id="type" value="{$type??''}">
						<input type="submit" name="select_type" value="全部" onclick="editorInput('-1','type')" class="btn btn-{$type==-1?'primary':'info'}">
						<input type="submit" name="select_type" value="图片" onclick="editorInput('1','type')" class="btn btn-{$type==1?'primary':'info'}">
						<input type="submit" name="select_type" value="涂鸦" onclick="editorInput('2','type')" class="btn btn-{$type==2?'primary':'info'}">
						<input type="submit" name="select_type" value="音频" onclick="editorInput('3','type')" class="btn btn-{$type==3?'primary':'info'}">
						<input type="submit" name="select_type" value="视频" onclick="editorInput('4','type')" class="btn btn-{$type==4?'primary':'info'}">
						<input type="submit" name="select_type" value="其他" onclick="editorInput('5','type')" class="btn btn-{$type==='5'?'primary':'info'}">
					</td>
					<td>
						&nbsp;<input type="checkbox" value="1" name="def" id="def" {$def?'checked':''}>
						&nbsp;<label for="def">只看默认公众号</label>
					</td>
				</tr>
				<tr>
					<td>
						<div>
							<span>排序方式</span>
							<input type="hidden" name="order_by_time" id="order_by_time" value="{$order_by_time??''}">
							<input type="hidden" name="order_by_size" id="order_by_size" value="{$order_by_size??''}">
							<input name="order" type="submit" value="时间降序" onclick="switchOrder('desc','order_by_time');" class="btn btn-<?=(isset($order_by_time) && $order_by_time=='desc')?'primary':'info';?>">
							<input name="order" type="submit" value="时间升序" onclick="switchOrder('asc','order_by_time');" class="btn btn-<?=(isset($order_by_time) && $order_by_time=='asc')?'primary':'info';?>">
							<input name="order" type="submit" value="大小降序" onclick="switchOrder('desc','order_by_size');" class="btn btn-<?=(isset($order_by_size) && $order_by_size=='desc')?'primary':'info';?>">
							<input name="order" type="submit" value="大小升序" onclick="switchOrder('asc','order_by_size');" class="btn btn-<?=(isset($order_by_size) && $order_by_size=='asc')?'primary':'info';?>">
							<span>注意：两种排序共存时，优先以时间排序</span>
						</div>
					</td>
					<td>
						<span onclick="upFile('img');" class="btn btn-info">上传图片</span>
						<span onclick="upFile('voice');" class="btn btn-info">上传语音</span>
						<span onclick="upFile('video');" class="btn btn-info">上传视频</span>
						<!--<span onclick="upMusic();" class="hand upspan">添加音乐</span>-->
						<span onclick="upFile('scrawl');" class="btn btn-info">上传涂鸦</span>
						<span onclick="upFile('other');" class="btn btn-info">其他</span>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<form action="{$public}file/index/editor" id=editor_form method="post" class="form-inline">
			{$error_msg??''}
			{$ecms_hashur['form']??''}
			<input type="hidden" name="operation_form" id='operation_form' value="editor_form" />
			<input type="hidden" id="editor_type" name="editor_type" value="" />
			<input type="hidden" name="site" id='site' value="" />
			<table class="table table-bordered table-hover text-center">
				<tr>
					<td colspan='15' align="left"><span class="msg-select">文件列表</span></td>
				</tr>
				<tr>
					<td width="3%">选择</td>
					<td width="3%">序号</td>
					<td width="5%">公众号</td>
					<td width="8%">昵称</td>
					<td width="10%">说明/备注</td>
					<td width="20%">预览</td>
					<td width="8%">大小（单位KB）</td>
					<td width="12%">更新时间</td>
					<td width="6%">类别</td>
					<!--<td width="6%">是否启用</td>-->
					<td>操作</td>
				</tr>
				{volist name="list" id="v" empty="<tr><td colspan=10>暂无数据</td></tr>"}
					<tr>
						<td><input name="ids[]" type="checkbox" value="{$i}" /></td>
						<td>{$i}<input name="id[]" type="hidden" value="{$v.id??''}" /></td>
						<td>{$v.aid??''}</td>
						<td>
							<input name="title[]" class="form-control" type="text" value="{$v.title??''}" 
								placeholder="必填" size="10" maxlength="20">
						</td>
						<td><textarea name="description[]" rows="3" class="form-control">{$v.description??''}</textarea></td>
						<td>
							<?php switch($v['type']){ case '图片': ?>
								<a title="点击可查看原图" href="<?=$v['path'].'/'.$v['name']?>" target="_blank">
									<img src="<?=$v['path'].'/'.$v['name']?>" width="120px">
								</a>
							<?php break; case '涂鸦': ?>
								<a title="点击可查看原图" href="<?=$v['path'].'/'.$v['name']?>" target="_blank">
									<img src="<?=$v['path'].'/'.$v['name']?>" width="120px">
								</a>
							<?php break; case '音频': ?>
								<br><audio src="<?=$v['path'].'/'.$v['name']?>" preload="none" controls >您的浏览器不支持 audio 标签。</audio>
							<?php break; case '视频': ?>
								<video id="thumb_view_{$i}" poster="{$v['thumb']}" src="<?=$v['path'].'/'.$v['name']?>" controls preload="none" style="max-width: 300px;">您的浏览器不支持video 标签。</video>
							<?php break; default: ?>
								<img src="<?=$v['path'].'/'.$v['name']?>" width="120px" alt="如不能显示：1、图片已删；2、路径出错；3、类型不支持预览">
							<?php } ?>
						</td>
						<td><?=isset($v['size'])?number_format($v['size']/1024,2):'';?></td>
						<td><?=$v['update_time']?></td>
						<td>
							{$v['type']??''}
						</td>
						<!-- <td>
							<select name="isok[]" disabled>
								<option value="1" <?=$v['is_ok']=='1'?'selected':''?> >开启</option>
								<option value="0" <?=$v['is_ok']=='0'?'selected':''?> >关闭</option>
							</select>
						</td>-->
						<td>
							<input name="thumb[]" type="hidden" id="thumb_{$i}" value="{$v['thumb']??''}">
							<?php if($v['type']=='视频'){ ?>
								<div class="form-group">
									<input type="button" class="btn btn-info form-control" onClick="selectThumb('{$i}', '{$v['id']}')" value="选择封面 "/>
									<!-- <div id="thumb_cover_{$i}" style="display:<?=$v['thumb']?'':'none'?>">
										<img src="{$v['thumb']}" alt="封面图片" width="120px" height="100" id="thumb_view_{$i}"/>&nbsp;<span onclick="thumb_delete('{$i}');" class="hand">删除</span>
									</div> -->
								</div>
							<?php } ?>
							<div class="form-group">
								<input value="修改" type="button" class="btn btn-primary form-control" <?=$aid==$v['aid']?'':'disabled'?> 
									onClick="editorInput('{$i}','site');editorInput('update','editor_type');editorModal('确定要这样修改吗？');" data-toggle='modal' data-target="#myModal">
							</div>
							<div class="form-group">
								<input value="删除" type="button" class="btn btn-danger form-control" <?=$aid==$v['aid']?'':'disabled'?> 
									onClick="editorInput('{$i}','site');editorInput('editor_form','operation_form');editorInput('oneDelete','editor_type');editorModal('确定要残忍删除吗？<br /><font class=&quot;text-danger&quot;>将会影响所有引用到该附件的“自动回复”、“群发记录”等<br>请谨慎操作！！！</font>');" 
										data-toggle="modal" data-target="#myModal">
							</div>
							<div class="form-group">
								<input value="{$v['lifecycle']?'微信端更新':'上传至微信'}" type="button" class="btn btn-info form-control" <?=$aid==$v['aid']?'':'disabled'?> 
									onClick="editorInput('{$i}','site');editorInput('editor_form','operation_form');editorInput('up_to_wx','editor_type');editorModal('确定要上传吗？');" 
									data-toggle="modal" data-target="#myModal">
							</div>
							<div class="form-group">
								<input value="更多" type="button" class="btn btn-info form-control" onClick="viewMore('{$i}','list_',this);">
							</div>
						</td>
					</tr>
					<tr id="list_more_{$i}" style="display: none;">
						<td colspan="11">
							<h4>地址：<?='http://'.$_SERVER['HTTP_HOST'].$v['path'].'/'.$v['name']?></h4>
							<h4>扩展名：{$v.ext}</h4>
							<h4>上传至微信时间：{$v.up_to_wx_time??''}</h4>
							<h4>创建时间：{$v.create_time??''}</h4>
						</td>
					</tr>
				{/volist}
				<tr>
					<td><input type="checkbox" onclick="checkall(form, 'ids')" name="all" /><br />全选</td>
					<td colspan="13">
						<input name="sDelete" type="button" class="btn btn-danger" value="批量删除" 
							onclick="editorInput('editor_form','operation_form');editorInput('sDelete','editor_type');
							editorModal('<span class=text-danger>确定要批量删除吗？<br>将会影响所有引用到被操作附件的“自动回复”、“群发记录”等<br>此操作极可能无法撤销，请谨慎操作！</span>');" 
							data-toggle="modal" data-target="#myModal"/></td>
				</tr>
				<?php if(isset($page)){?>
					<tr align="left">
						<td colspan="12">
							<?php echo $page;?>
						</td>
					</tr>
				<?php }?>	
			</table>
		</form>
	</div>
</div>
<script id="container" name="content" type="text/plain" style="display:none"></script>
<script id="container2" name="content" type="text/plain" style="display:none"></script>
<script>
var ue=UE.getEditor('container',{isShow : false});
var diyUe=UE.getEditor('container2',{isShow : false});
ue.ready(function(){
	var options = {
		'classid' : '{$lur.classid??""}',
		'filepass': '{$lur.filepass??""}',
		'userid'  : '<?=isset($isadmin)?(isset($logininid)?$logininid:''):(isset($lur['userid'])?$lur['userid']:'');?>',
		'username': '<?=isset($isadmin)?(isset($loginin)?$loginin:''):(isset($lur['username'])?$lur['username']:'')?>',
		'rnd'     : '<?=isset($isadmin)?(isset($loginrnd)?$loginrnd:''):(isset($lur['rnd'])?$lur['rnd']:'')?>'
    };
	ue.execCommand('serverparam', options);
	diyUe.execCommand('serverparam', options);
});
var myImg;
function upFile(type){

	if(type=='img'){
		myImg=ue.getDialog("insertimage");
		myImg.open();
		ue.addListener('afterInsertImage', function (t, arg){
			list_form.submit();
		});
	}else if(type=='voice'){
		var myVoice=ue.getDialog("insertvideo");
		myVoice.title = '语音';
		myVoice.open();
	 	ue.addListener('contentChange', function (t, arg){
	 		list_form.submit();
	 	});
	}else if(type=='video'){
		var myVideo=ue.getDialog("insertvideo");
		myVideo.open();
	 	ue.addListener('contentChange', function (t, arg){
	 		list_form.submit();
	 	});
	}else if(type=='scrawl'){
		var myScrawl=ue.getDialog("scrawl");
		myScrawl.open();
	 	ue.addListener('afterInsertImage', function (t, arg){
	 		list_form.submit();
	 	});
	}else{
		var files=ue.getDialog("attachment");
		files.open();
	 	ue.addListener('contentChange', function (t, arg){
	 		list_form.submit();
	 	});
	}
}
var hasRegistAfterInsertImage = false;
function selectThumb(id, videoId){
	myImg=diyUe.getDialog("insertimage");
	var listener = function (t, arg){
		//$('#thumb_cover_'+id).show();
		$('#thumb_'+id).val(arg[0].src);
		//$('#thumb_view_'+id).attr('src',arg[0].src);
		$('#thumb_view_'+id).attr('poster',arg[0].src);
		$.ajax({
			url:'<?php echo url("/file/index/updateTitleImg")?>?id='+videoId+'<?php echo isset($ecms_hashur['href'])?$ecms_hashur['href']:'';?>',
			dataType:'json',
			type:'get',
			data:{'path':arg[0].src, 'name':arg[0].alt},
			success:function(r){
				if(r.errCode == 0){
					alert("设置成功");
				}else{
					alert(r.errMsg);
				}
			},
			error:function(r){
				alert('设置失败');
			},
			complete:function(){
				diyUe.removeListener('afterInsertImage', listener);
			}
		});
	};
	diyUe.addListener('afterInsertImage', listener);
	myImg.open();
	myImg.addListener('close', function(){
		setTimeout(function(){
    		diyUe.removeListener('afterInsertImage', listener);			
		})
	});
}

function thumb_delete(id){
	$('#thumb_cover_'+id).hide();
	$('#thumb_'+id).val('');
}
function switchOrder(val,id){
	var obj=$('#'+id);
	var v=obj.val();
	if(v==val){
		obj.val('');
	}else{
		obj.val(val);
	}
}

function viewMore(site,type,obj){
	type=type || 'list_';
	if($(obj).val()=='更多'){
		$('#'+type+'more_'+site).show();
		$(obj).val('隐藏');
	}else{
		$('#'+type+'more_'+site).hide();
		$(obj).val('更多');
	}
}
</script>