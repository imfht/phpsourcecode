<?php defined('APP_PATH') OR exit('No direct script access allowed'); ?>

<div class="row" >
	<div class="col-lg-12">
		<h4>位置：{$title}</h4>
	</div>
</div>
<form action="{$public}user/index/editor" id="view_form" method="post" class="form-inline">
	{$error_msg??''}
	{$ecms_hashur['form']??''}
	<div class="row">
		<div class="col-lg-12">
			<input type="hidden" name="operation_form" id='operation_form' value="" />
			<input type="hidden" id="operation" name="operation" value="" />
			<input type="hidden" id="editor_type" name="editor_type" value="" />
			<input type="hidden" name="site" id='site' value="" />
			<table class="table table-bordered table-hover text-left"> 
				<tr>
					<td>关注者筛选</td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
							<input value="刷新关注者列表" type="button" class="btn btn-primary" 
								onClick="editorInput('refresh_list','editor_type');editorInput('view_form','operation_form');
									editorModal('确定要刷新吗？&lt;h4&gt;&lt;span class=text-danger&gt;该操作会先清空本地记录;&lt;br&gt;且当用户数较多时，耗时较长；&lt;br&gt;一旦开始，不能终止，也不应该终止，否者容易出现意外错误&lt;/span&gt;&lt;/h4&gt;');" 
								data-toggle='modal' data-target="#myModal">
						</div>
						<div class="form-group">
							<input name="search" type="search" class="form-control" value="{$search??''}" placeholder="请输入搜索内容">
						</div>
						<div class="form-group">
							<input name="doSearch" type="submit" value="搜索" class="btn btn-warning">&nbsp;
							<?php echo isset($search)?'正在搜索：'.$search:'';?>
						</div>
					</td>
				</tr>
			</table>
			
		</div>
	</div>
</form>
	<div class="row">
		<div class="col-lg-12">	
			<table class="table table-bordered table-hover text-center">
				<tr align="left">
					<td colspan='9'>关注者列表</td>
				</tr>
				<tr class="tablebgcolor">
					<td width="4%">序号</td>
					<td width="8%">头像</td>
					<td width="5%">公众号id</td>
					<td width="8%">用户昵称</td>
					<td width="4%">性别</td>
					<td width="6%">城市</td>
					<td width="10%">备注</td>
					<td width="8%">分组</td>
					<td>操作</td>
				</tr>
				{volist name="list" id="user" key="k" empty="<tr><td  colspan=9>暂无关注者</td></tr>"}
					<tr>
						<td>{$k}</td>
						<td><img src="{$user.head_img_url??''}" height="80px"></td>
						<td>{$aid??''}</td>
						<td>{$user.nick_name??''}</td>
						<td>{$user.sex??''}</td>
						<td>{$user.city??''}</td>
						<td>{$user.remark??''}</td>
						<td>{$user.groupid??''}</td>
						<td>
							<a class="btn btn-success" href="{$public}reply/hand/index?type=user&id={$user.id??''}{$ecms_hashur['href']??''}">回复</a>
							<input value="修改" type="button" class="btn btn-primary" <?=(isset($user['aid']) && $user['aid']==$aid)?'':'disabled';?> 
								onClick="editorInput('{$k}','site');editorModal('暂不支持修改！');" 
								data-toggle='modal' data-target="#myModal">
							<input value="更多" type="button" class="btn btn-info" <?=(isset($user['aid']) && $user['aid']==$aid)?'':'disabled'?> onClick="viewMore('{$k}','list_',this);">
						</td>
					</tr>
					<tr id="list_more_{$k}" style="display: none;">
						<td colspan="9">
							<h4 class="text-success">国家：{$user.country??''}&nbsp;&nbsp;<span class="text-danger">省：{$user.province??''}</span>&nbsp;&nbsp;<span class='text-primary'>语言：{$user.language??''}</span></h4>
							<h4>关注时间：{$user['subscribe_time']??''}</h4>
						</td>
					</tr>
				{/volist}     
				<?php if(isset($page)){?>
					<tr align="left">
						<td colspan="9">
							<?php echo $page;?>			
						</td>
					</tr>
				<?php }?>
			</table>
		</div>
	</div>
</form>
<script>
function tPage(obj,form){
	var page=$(obj).attr('data-ci-pagination-page');
	$('#tpage').val(page);
	form = form || 'editor_form';
	$('#editor_type').val('tpage');
	$('#'+form).submit();
	//Ajax(u,a);
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