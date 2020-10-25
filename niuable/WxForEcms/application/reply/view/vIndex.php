<?php defined('APP_PATH') OR exit('No direct script access allowed'); ?>

<div class="row" >
	<div class="col-lg-12">
		<h4>位置：{$title}</h4>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<!-- 头部 导航 -->
		<ul class="nav nav-pills">
			<li {$panel==1?'class="active"':''}><a href="{$public??''}reply/index/index/?panel=1{$ecms_hashur['href']??''}&def={$def?? '1'}">关键词回复列表</a></li>
			<li {$panel==2?'class="active"':''}><a href="{$public??''}reply/index/index/?panel=2{$ecms_hashur['href']??''}&def={$def?? '1'}">关注时回复</a></li>
			<li {$panel==3?'class="active"':''}><a href="{$public??''}reply/index/index/?panel=3{$ecms_hashur['href']??''}&def={$def?? '1'}">无匹配回复</a></li>
			<li {$panel==4?'class="active"':''}><a href="{$public??''}reply/index/index/?panel=4{$ecms_hashur['href']??''}&def={$def?? '1'}">增加自动回复</a></li>
			{if condition="isset($panel) && $panel==5"}
		   		<li class="active"><a href="#">修改自动回复</a></li>
			{/if}
		</ul>
	</div>
</div>
<br>
<div class="row">
	<div class="col-lg-12">	
		<!-- 关键词自动回复列表 -->
		{if (isset($panel) && $panel==1)}
			<form action="{$public}reply/index/index" id="list-form" class="form-inline">
				{$ecms_hashur['form'] ?? ''}
				<table class="table table-bordered table-hover text-center">
					<tr align="left">
						<td colspan="10">
							<div class="form-inline">
								<div class="form-group">
									<input type="checkbox" value="1" name="def" id="def" <?=$def?'checked':''?> 
										onclick="editorInput('refresh_list','operation');javascript:this.form.submit()">
									<label for="def">&nbsp;只看默认公众号</label>
								</div>
								<div class="form-group">
									<input name="search" type="search" class="form-control" value="{$search??''}" placeholder="请输入搜索内容">
								</div>
								<div class="form-group">
									<input name="doSearch" type="submit" value="搜索" class="btn btn-warning">&nbsp;
									<?php echo isset($search)?'正在搜索：'.$search:'';?>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</form>
			<form action="{$public}reply/index/editor" id="form">
				{$ecms_hashur['form'] ?? ''}
				<input type="hidden" id="site" name="site" value="" />
				<input type="hidden" id="operation" name="editor_type" value="update" />
				<input type="hidden" id="operation_form" name="operation_form" value="" />
				<table class="table table-bordered table-hover text-center" style="vertical-align: middle">
					<tr>
						<td width="4%">选择</td>
						<td width="3%">序号</td>
						<td width="6%">ID</td>
						<td width="10%">关键字</td>
						<td width="8%">模糊查询</td>
						<td width="8%">状态</td>
						<td width="10%">回复类型</td>
						<td width="12%">用于公众号</td>
						<td width="8%">菜单key值</td>
						<td>操作</td>
					</tr>
					{volist name="list" key="k" id="v" empty="<tr><td colspan=10>暂无数据</td></tr>"}
						<tr>
							<td>
								<input name="ids[]" type="checkbox" id="ids" value="{$k}" <?=$aid==$v['aid']?'':'disabled';?>/>
							</td>
							<td>{$k}</td>
							<td><input name="id[]" type="hidden" value="{$v.id}"/>{$v.id}</td>
							<td><input name="keyword[]" type="hidden" value="{$v.keyword}"/>{$v.keyword}</td>
							<td><select name="is_like[]" size="1">
									<option value="0" {$v.is_like?'':'selected="selected"'}>精确</option>
									<option value="1" {$v.is_like?'selected="selected"':''}>模糊</option>
								</select>
							</td>
							<td><select name="is_ok[]" size="1">
									<option value="0">停用</option>
									<option value="1" {$v.is_ok?'selected="selected"':''}>启用</option>
								</select>
							</td>
							<td>
								<?php
									if ($v['msg_type']=='news') { echo '图文'; }
									elseif ($v['msg_type']=='text'){ echo '文本';}
									elseif ($v['msg_type']=='voice'){ echo '音频';}
									elseif ($v['msg_type']=='video'){ echo '视频';}
									elseif ($v['msg_type']=='img'){ echo '图片';}
								?>
							</td>
							<td><input name="aid[]" type="hidden" value="{$v.aid}"/>{$v.aid}</td>
							<td><select name="is_menu_key[]" size="1">
									<option value="0">否</option>
									<option value="1" <?=$v['is_menu_key']?'selected="selected"':'';?>>是</option>
								</select>
							</td>
							<td colspan="5">
								<input type="button" value="更新" <?=$aid==$v['aid']?'':'disabled';?> class="btn btn-info" 
									onclick="setSite(<?=$k?>);
										editorInput('update','operation');
										editorModal('您确定要更新吗？');
										editorInput('form','operation_form');" 
										data-toggle="modal" data-target="#myModal"/>
								<input type="button" value="删除" <?=$aid==$v['aid']?'':'disabled';?> class="btn btn-danger" 
									onclick="setSite(<?=$k?>);
										editorInput('delete','operation');
										editorModal('您确定要删除吗？');
										editorInput('form','operation_form');" 
										data-toggle="modal" data-target="#myModal"/>
								<a  class="btn btn-success" <?=$aid==$v['aid']?'':'disabled';?> 
									href="<?=$aid==$v['aid']?$public.'reply/index/find/'.'?id='.$v['id'].$ecms_hashur['href'].'&def=1':'javascript:void(0)';?>">更多</a>
							</td>
						</tr>
					{/volist}
					<tr>
						<td>
							<label>
								<input type="checkbox" onclick="checkall(form, 'ids')" name="all" />
								<br />
								全选</label>
						</td>
						<td colspan="9">
							<input type="button" class="btn btn-danger" name="sDelete" value="删除所选" 
								onclick="editorInput('sDelete','operation'); editorModal('您确定要删除所选吗？&lt;br&gt; &lt;font class=text-danger&gt;该操作极可能不可恢复，请谨慎操作！&lt;/font&gt;');editorInput('form','operation_form');" data-toggle="modal" data-target="#myModal" />
							<input type='button' class="btn btn-success" value='批量更新' 
								onclick="editorInput('sRefresh','operation');
									editorModal('您确定要批量更新吗？&lt;br&gt; &lt;font class=text-danger&gt;该操作极可能不可恢复，请谨慎操作！&lt;/font&gt');
									editorInput('form','operation_form');" 
									data-toggle="modal" data-target="#myModal"/>
							<a href="{$public}reply/index/index?{$ecms_hashur['href']??''}" class="btn btn-info" >刷新</a>
						</td>
					</tr>
					<?php if (empty($page)) { }else {?>
						<tr align="left">
							<td colspan="10">
								<?php echo $page;?>
							</td>
						</tr>
					<?php }?>
				</table>
			</form>
		<!-- /自动回复列表 -->
		
		<!-- 关注时自动回复 -->
		{elseif isset($panel) && $panel==2 /}
			{$error_msg??''}
			<form action="{$public}reply/index/{$id?'editor':(isset($form.id)?'editor':'add')}/reply_type/2" id='editor_form' class="form-inline">
				<table class="table table-bordered table-hover text-center">
					{$ecms_hashur['form']??''}
					<input type="hidden" name="operation_form" id='operation_form' value="">
					<input type="hidden" name="id" id="id" value="{$id?$id:(isset($form.id)?$form.id:'')}">
					<tr align="left">
						<td>&nbsp;&nbsp;用于公众号：默认</td>
					</tr>
		            <tr>
						<td>
							<div class=form-group >
								<label class="control-label" for=open>开启：</label>
		    					<input type="radio" name="is_ok" id="open" value="1" 
		    						<?php echo (!isset($is_ok) || $is_ok==1 || (isset($form['is_ok']) && $form['is_ok']==1))?'checked':'';?>>
							</div>&nbsp;&nbsp;&nbsp;&nbsp;
							<div class="form-group">
								<label class="control-label text-right" for=close>关闭：</label>
		    					<input type="radio" name="is_ok" id="close" value="0" 
		    						<?php echo (isset($is_ok) && $is_ok=='0' || (isset($form['is_ok']) && $form['is_ok']==0))?'checked':'';?>>
								
							</div>&nbsp;&nbsp;&nbsp;&nbsp;
							<div class=form-group>
								<label class="control-label col-xs-6 text-right" style="padding-top: 7px;" for=jumpMenu2>回复类型：</label>
									<div class="col-xs-3" style="max-width: 110px;">
									<select name="msg_type" id="jumpMenu2" onchange="viewReply(this.value,'reply')" class="form-control">
										<option value="text">文本</option>
										<option value="img" {$msg_type=='img'?"selected":(isset($form.msg_type) && $form.msg_type=='img'?"selected":'')}>图片</option>
										<option value="voice" {$msg_type=='voice'?"selected":'';}>语音</option>
										<option value="video" {$msg_type=='video'?"selected":''}>视频</option>
										<option value="news" {$msg_type=='news'?"selected":''}>图文</option>
									</select>
									</div>
							</div>&nbsp;&nbsp;&nbsp;&nbsp;
							<div class="form-group">
								<label class="control-label text-right" for=is_menu_key>菜单Key：</label>
		    					<input style="margin-left: 3px;" type="checkbox" value="1" name="is_menu_key" id="is_menu_key" 
		    						{$is_menu_key==1 || (isset($form.is_menu_key) && $form.is_menu_key==1)?'checked':''}/>
							</div>
						</td>
					</tr>
					<tr><td>
						<div class="col-xs-8 col-lg-offset-2 ">{$reply_content??''}</div>
						<div class="col-lg-2 col-xs-4 text-danger" >
							{$form_error.text??''}
							{$form_error.img??''}
							{$form_error.news??''}
							{$form_error.voice??''}
							{$form_error.video??''}
						</div>
					</td></tr>
					<tr>
						<td> 
							<input name="reply_id" type='hidden' id="reply_id" value="{$reply_id??''}"  />
							<input type="reset" class="btn btn-primary" id="button" onclick="viewReply(jumpMenu2_value,'reply')" value="重置" />
							<input type="button" class="btn btn-success" value="提交" 
								onclick="editorInput('editor_form','operation_form');editorModal('确定要修改吗？');" 
								data-toggle="modal" data-target="#myModal"/>
						</td>
					</tr>
				</table>
			</form>
		<!-- /关注时自动回复 -->
		
		<!-- 无匹配时自动回复 -->
		{elseif isset($panel) && $panel==3 /}
			{$error_msg??''}
			<form action="{$public}reply/index/{$id?'editor':(isset($form.id)?'editor':'add')}/reply_type/3" id='editor_form' class="form-inline" role="form">
				<table class="table table-bordered table-hover text-center">
					{$ecms_hashur['form']??''}
					<input type="hidden" name="operation_form" id='operation_form' value="">
					<input type="hidden" name="id" id="id" value="{$id?$id:(isset($form.id)?$form.id:'')}">
					<tr align="left">
						<td>&nbsp;&nbsp;用于公众号：默认</td>
					</tr>
		            <tr>
						<td>
							<div class=col-xs-12>
								<div class="form-group">
									<label class="control-label " for=open>开启：</label>
			    					
				    					<input type="radio" name="is_ok" id="open" value="1" 
				    						<?php echo (!isset($is_ok) || $is_ok==1 || (isset($form['is_ok']) && $form['is_ok']==1))?'checked':'';?>>
			    					
								</div>&nbsp;&nbsp;&nbsp;&nbsp;
								<div class="form-group">
									<label class="control-label text-right" for=close>关闭：</label>
			    					<input type="radio" name="is_ok" id="close" value="0" 
			    						<?php echo (isset($is_ok) && $is_ok=='0' || (isset($form['is_ok']) && $form['is_ok']==0))?'checked':'';?>>
									
								</div>&nbsp;&nbsp;&nbsp;&nbsp;
								<div class=form-group>
									<label class="control-label col-xs-6 text-right" style="padding-top: 7px;" for=jumpMenu2>回复类型：</label>
									<div class="col-xs-3" style="max-width: 110px;">
									<select name="msg_type" id="jumpMenu2" onchange="viewReply(this.value,'reply')" class="form-control">
										<option value="text">文本</option>
										<option value="img" {$msg_type=='img'?"selected":(isset($form.msg_type) && $form.msg_type=='img'?"selected":'')}>图片</option>
										<option value="voice" {$msg_type=='voice'?"selected":(isset($form.msg_type) && $form.msg_type=='voice'?"selected":'')}>语音</option>
										<option value="video" {$msg_type=='video'?"selected":(isset($form.msg_type) && $form.msg_type=='video'?"selected":'')}>视频</option>
										<option value="news" {$msg_type=='news'?"selected":(isset($form.msg_type) && $form.msg_type=='news'?"selected":'')}>图文</option>
									</select>
									</div>
								</div>&nbsp;&nbsp;&nbsp;&nbsp;
								<div class="form-group">
									<label class="control-label text-right" for=is_menu_key>菜单Key：</label>
			    					<input style="margin-left: 3px;" type="checkbox" value="1" name="is_menu_key" id="is_menu_key" 
			    						{$is_menu_key==1 || (isset($form.is_menu_key) && $form.is_menu_key==1)?'checked':''}/>
								</div>
							</div>
						</td>
					</tr>
					<tr><td>
						<div class="col-xs-8 col-lg-offset-2 ">{$reply_content??''}</div>
						<div class="col-lg-2 col-xs-4 text-danger" >
							{$form_error.text??''}
							{$form_error.img??''}
							{$form_error.news??''}
							{$form_error.voice??''}
							{$form_error.video??''}
						</div>
					</td></tr>
					<tr>
						<td> 
							<input name="reply_id" type='hidden' id="reply_id" value="{$reply_id??''}"  />
							<input type="reset" class="btn btn-primary" id="button" onclick="viewReply(jumpMenu2_value,'reply')" value="重置" />
							<input type="button" class="btn btn-success" value="提交" 
								onclick="editorInput('editor_form','operation_form');editorModal('确定要修改吗？');" 
								data-toggle="modal" data-target="#myModal"/>
						</td>
					</tr>
				</table>
			</form>
		<!-- 无匹配时自动回复 -->	
		
		<!-- 新增关键词自动回复 -->
		{else /}
			{$error_msg??''}
			<form action="{$public}reply/index/{$panel==4?'add':'editor'}/reply_type/1" id="editor_form" 
				method="post" class="form-horizontal">
				{$ecms_hashur['form']??''}
				{if isset($panel) && $panel==5}
	            	<input type="hidden" name="id" value="{$id??''}" />
	            {/if}
				<input type="hidden" name="operation_form" id='operation_form' value="">
				<table class="table table-bordered table-hover text-center">
					<tr align="left">
						<td colspan="6">用于公众号：默认</td>
					</tr>
					<tr>
						<td>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-xs-3 text-right" for=keyword>关键词：</label>
									<div class=col-xs-5>
										<input type="text" name="keyword" id='keyword' value="{$keyword?$keyword:((isset($form.keyword) && ($form.keyword || $form.keyword===0))?$form.keyword:'' )}" class=form-control placeholder="必填"/>
									</div>
									{$form_error['keyword']?'<div class="col-sm-4" style="color:red">'.$form_error['keyword'].'</div>' :''}
								</div>
								<div class=form-group>
									<label class="control-label col-xs-3 text-right" for=is_like>模糊查询：</label>
									<div class=col-xs-5>
										<select name="is_like" id="is_like" class=form-control>
											<option value="0">关闭</option>
											<option value="1" {$is_like?"selected":''}>开启</option>
										</select>
									</div>
								</div>
								<div class=form-group>
									<label class="control-label col-xs-3 text-right" for=jumpMenu2>回复类型：</label>
									<div class=col-xs-5>
										<select name="msg_type" id="jumpMenu2" onchange="viewReply(this.value,'reply')" class=form-control>
											<option value="text">文本</option>
											<option value="img" {$msg_type=='img'?"selected":''}>图片</option>
											<option value="voice" {$msg_type=='voice'?"selected":''}>语音</option>
											<option value="video" {$msg_type=='video'?"selected":''}>视频</option>
											<option value="news" {$msg_type=='news'?"selected":''}>图文</option>
										</select>
									</div>
								</div>
							</div>
							<div class=col-md-6>
								<div class=form-group>
									<label class="control-label col-xs-3 text-right" for=open>开启：</label>
									<div class="col-xs-5 text-left" style="padding-top: 7px;padding-left:0px">
			    						<input type="radio" name="is_ok" id="open" value="1" 
			    							<?php echo (!isset($is_ok) || $is_ok==1 || (isset($form['is_ok']) && $form['is_ok']==1))?'checked':'';?>>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-xs-3 text-right" for=close>关闭：</label>
									<div class="col-xs-5 text-left" style="padding-top: 7px;padding-left:0px">
			    						<input type="radio" name="is_ok" id="close" value="0" 
			    							<?php echo (isset($is_ok) && $is_ok=='0' || (isset($form['is_ok']) && $form['is_ok']==0))?'checked':'';?>>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-xs-3 text-right" for=is_menu_key>菜单Key：</label>
									<div class="col-xs-5 text-left" style="padding-top: 7px;padding-left:0px">
			    						<input style="margin-left: 3px;" type="checkbox" value="1" name="is_menu_key" id="is_menu_key" 
			    							{$is_menu_key==1 || (isset($form.is_menu_key) && $form.is_menu_key==1)?'checked':''}/>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<tr><td colspan="6">
						<div class="col-lg-8 col-lg-offset-2 col-sm-8">{$reply_content??''}</div>
						<div class="col-lg-2 col-sm-4 text-danger" >
							{$form_error.text??''}
							{$form_error.img??''}
							{$form_error.news??''}
							{$form_error.voice??''}
							{$form_error.video??''}
						</div>
					</td></tr>
					<tr>
						<td colspan="6">
							<input type="reset" class="btn btn-primary" name="button" id="button" onclick="viewReply(jumpMenu2_value,'reply')" value="重置" />
							<input type="button" class="btn btn-success" value="提交" 
								onclick="editorInput('editor_form','operation_form');editorModal('确定要{$panel==4?\'新增\':\'修改\'}吗？');" 
								data-toggle="modal" data-target="#myModal"/>
						</td>
					</tr>
				</table>
			</form>
		{/if}

	</div>
</div>
<div id="select_area" style="display:none;" tabindex="-1">
    <div id="select_area_1">
    	
    </div>
</div>
<script>
<?php if (isset($panel) && $panel!=1) { ?>
	var jumpMenu2_value=document.getElementById('jumpMenu2').value;
<?php }?>

	function loadModal(id){
		$('#'+id).modal('show');
	}
	function More(e){
		//alert(e.alt);
		$("#select_area_1").html("<iframe id='iframe_image' src='"+e.alt+"' ></iframe>");
		//$("#select_area_1").html="<iframe id='iframe_image' src='"+e.alt+"' ></iframe>";
		$("#select_area").show();
		alert(e.name);
	}
	function sure2(u){
		u = u || "{$public}reply/index/editor?{$ecms_hashur['href']??''}";
		a=$('#form').serialize();
		Ajax(u,a);
	}
	function Ajax(u,a){
		$.ajax({
			url:u,// ---- url路径，根据需要写,
			type:'post',
			data:a,
			dataType: "json",
			timeout:300000,//5分钟
			beforeSend:function(XMLHTTPRequest){
			   //alert('远程调用开始...');
			   //$("#loading").html("正在发送...");
			},
			success:function(data,textStatus){
				//var ajaxobj = $.parsejson(data);
				//alert('返回值：'+data.result);
				
				//$("#body").html(data);
				if(data.error=="0"){
					$(function(){
						$('#myModal').modal('hide');
						$('#myModal').removeData("bs.modal");
						editorModal('正在刷新，请稍等……','请注意');
						//document.getElementById('myModalLabel').innerHTML='请注意';
						//$('#myModal .modal-body')[0].html='';
						$('#myModal').modal('toggle');
						//setTimeout(function(){},500);
						
						
					});
					self.location.href="{$public}reply/index/index/reply_type/1{$ecms_hashur['href']??''}";
				}else if(data.error==1){
					alert('发生了错误：'+data.message);
				}else{
					alert(发生了一个未知错误);
				}
				//alert('更新成功'+data);
				//$("body").html(data);
				return false;
			},
	
			error:function(XMLHTTPRequest,textStatus,errorThrown){
				alert('获取数据错误；error状态文本值：'+textStatus+" 异常信息："+errorThrown);
				return false;
			},
		});
	}
</script>