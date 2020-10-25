<?php defined('APP_PATH') OR exit('不允许访问'); ?>
<div class="row" >
	<div class="col-lg-12">
		<h4>位置：{$title}</h4>
	</div>
</div>
<div>
<form action="{$public}menu/index/{$editor_type=='add'?'add':'editor'}" id="editor_form" class="form-horizontal">
{$error_msg??''}
{$ecms_hashur['form']??''}
<input type="hidden" name="editor_type" id="editor_type" value="<?php echo isset($editor_type)?$editor_type:(isset($form['editor_type'])?$form['editor_type']:'add');?>">
<input type="hidden" name="operation_form" id='operation_form' value="editor_form" />
<input type="hidden" name="id" id="id" value="{$all_menu.id ?$all_menu.id:(isset($form['id'])?$form['id']:'1')}">
<input type="hidden" name="aid" id="aid" value="{$aid?$aid:(isset($form['aid'])?$form['aid']:'1')}">
	<div class="row">
		<div class="col-lg-4 col-sm-5">
			<div class="form-group">
				<label for=menu_title class="control-label col-md-2 col-xs-4">标题：</label>
				<div class="col-xs-8">
					<input type=text name=title id="menu_title" class="form-control"
						value="<?php echo isset($all_menu['title'])?$all_menu['title']:(isset($form['title'])?$form['title']:'');?>">
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<!-- 菜单列表 -->
		<div class="col-lg-4 col-sm-5">
			<div class="panel panel-default">
				<div class="panel-heading">菜单管理</div>
				<ul class="list-group">
					{for start="0" end="3" step="1" name="i" }
						{for start="0" end="6" step="1" name="j" }
							{if $j==0}
								<li class="list-group-item active" style="cursor: pointer;">
									<div id="menu_{$i}_0" alt="{$i}_{$j}" onclick="showMenu2(this);">
										<span id="menu_title_{$i}_0" {$form_error.error_title[$i][$j]?'style="color:red;"':''}>
											<?php echo isset($menu[$i]["name"])?$menu[$i]["name"]:((isset($form["name"][$i][$j]) && (!empty($form['name'][$i][$j]) || $form['name'][$i][$j]==='0') )?$form["name"][$i][$j]:"请添加");?>
										</span>
									</div>
								</li>
							{else /}
								<li id="menu_{$i}_{$j}" class="list-group-item" 
									<?php echo (isset($menu[$i]['sub_button'][$j-1]) 
									|| (isset($form["name"][$i][$j]) &&  $form["name"][$i][$j])
									|| (isset($form["click"][$i][$j]) &&  $form["click"][$i][$j])
									|| (isset($form["view"][$i][$j]) &&  $form["view"][$i][$j])
									|| (isset($form["scancode_push"][$i][$j]) &&  $form["scancode_push"][$i][$j])
									|| (isset($form["scancode_waitmsg"][$i][$j]) &&  $form["scancode_waitmsg"][$i][$j])
									|| (isset($form["pic_sysphoto"][$i][$j]) &&  $form["pic_sysphoto"][$i][$j])
									|| (isset($form["pic_photo_or_album"][$i][$j]) &&  $form["pic_photo_or_album"][$i][$j])
									|| (isset($form["pic_weixin"][$i][$j]) && $form["pic_weixin"][$i][$j])
									|| (isset($form["location_select"][$i][$j]) &&  $form["location_select"][$i][$j]))?'':'style="display:none"';?>>
									<div class="row">
										<div class="text-center col-sm-8" alt="{$i}_{$j}" onclick="showMenu2(this);" style="cursor: pointer;">
											<h5 class="text-info" {$form_error.error_title[$i][$j]?'style="color:red;"':''} id="menu_title_{$i}_{$j}">
												<?=isset($menu[$i]['sub_button'][$j-1]["name"])?$menu[$i]['sub_button'][$j-1]["name"]:((isset($form["name"][$i][$j]) && (!empty($form["name"][$i][$j]) || $form["name"][$i][$j]==='0'))?$form["name"][$i][$j]:"请添加")?>
											</h5>
										</div>
										<div class="col-sm-4"><span class="btn btn-danger" onclick="deleteMenu('{$i}_{$j}')" >删</span></div>
									</div>
									
								</li>
							{/if}
							{if $j==5}
								<li class="list-group-item text-center" onclick="viewMenu('{$i}')" >
									<input type="button" value="增加" class="btn btn-success">
								</li>
							{/if}
						{/for}
					{/for}
				</ul>
			</div>
		</div>
		<!-- 菜单内容 -->
		<div class="col-lg-8 col-sm-7">
			{for start="0" end="3" step="1" name="i" }
				{for start="0" end="6" step="1" name="j" }
					<?php if($j==0){
							$v=isset($menu[$i])?$menu[$i]:array();
							
						}else{
							$v=isset($menu[$i]['sub_button'][$j-1])?$menu[$i]['sub_button'][$j-1]:array();
						}
					?>
					<div id="menu_content_main_border_{$i}_{$j}" 
						<?=($i==0 && $j==0)?'':'style="display:none"'?>>
						<div class="form-group">
							<label for="menu_content_name_{$i}_{$j}" class="col-lg-2 col-sm-4 control-label">菜单名：</label>
							<div class="col-lg-6 col-sm-4">
								<input type="text" class="form-control" id="menu_content_name_{$i}_{$j}" alt="{$i}_{$j}" name="name[{$i}][]" 
									<?php echo ($j==0 || isset($menu[$i]['sub_button'][$j-1]) 
									|| (isset($form["name"][$i][$j]) &&  $form["name"][$i][$j])
									|| (isset($form["click"][$i][$j]) &&  $form["click"][$i][$j])
									|| (isset($form["view"][$i][$j]) &&  $form["view"][$i][$j])
									|| (isset($form["scancode_push"][$i][$j]) &&  $form["scancode_push"][$i][$j])
									|| (isset($form["scancode_waitmsg"][$i][$j]) &&  $form["scancode_waitmsg"][$i][$j])
									|| (isset($form["pic_sysphoto"][$i][$j]) &&  $form["pic_sysphoto"][$i][$j])
									|| (isset($form["pic_photo_or_album"][$i][$j]) &&  $form["pic_photo_or_album"][$i][$j])
									|| (isset($form["pic_weixin"][$i][$j]) && $form["pic_weixin"][$i][$j])
									|| (isset($form["location_select"][$i][$j]) &&  $form["location_select"][$i][$j]))?'':'disabled';?> value="<?php echo isset($v['name'])?$v['name']:(isset($form["name"][$i][$j])?$form["name"][$i][$j]:'') ?>" onchange="changeTitle(this);">
							</div>
							{$form_error.name[$i][$j]??''}
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2 col-sm-4" for="menu_content_type_{$i}_{$j}">类型：</label>
							<div class="col-lg-6 col-sm-4">
								<select class="form-control" name="type[{$i}][]" size="1" id="menu_content_type_{$i}_{$j}" alt="{$i}_{$j}" onchange="showMenu(this);">
				                    <option value='click' <?=!isset($v['type']) || ($v['type']=='click' || empty($v['type']))?'selected="selected"':''?>>点击推事件</option>
				                    <option value="view" <?=((isset($v['type']) && $v['type']=='view') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='view'))?'selected="selected"':''?>>跳转URL</option>
				                    <option value="scancode_push" <?=((isset($v['type']) && $v['type']=='scancode_push') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='scancode_push'))?'selected="selected"':''?>>扫码推送事件</option>
				                    <option value="scancode_waitmsg" <?=((isset($v['type']) && $v['type']=="scancode_waitmsg") || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='scancode_waitmsg'))?'selected="selected"':''?>>扫码推送事件并等待</option>
				                    <option value="pic_sysphoto" <?=((isset($v['type']) && $v['type']=="pic_sysphoto") || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='pic_sysphoto'))?'selected="selected"':''?>>弹出系统拍照发图</option>
				                    <option value="pic_photo_or_album" <?=((isset($v['type']) && $v['type']=='pic_photo_or_album') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='pic_photo_or_album'))?'selected="selected"':''?>>弹出拍照或者相册发图</option>
				                	<option value="pic_weixin" <?=((isset($v['type']) && $v['type']=='pic_weixin') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='pic_weixin'))?'selected="selected"':''?>>弹出微信相册发图器</option>
				                	<option value="location_select" <?=((isset($v['type']) && $v['type']=='location_select') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='location_select'))?'selected="selected"':''?>>发送位置</option>
								</select>
							</div>
							<div class="col-lg-4 col-sm-4"></div>
						</div>
						
						<div class="form-group" id="menu_content_border_click_{$i}_{$j}" <?=((!isset($v['type']) && (!isset($form["type"][$i][$j]) || $form["type"][$i][$j]=='click')) || (isset($v['type']) && $v['type']=='click'))?'':'style="display:none"'?>>
							<label class="control-label col-lg-2 col-sm-4" for="menu_content_click_{$i}_{$j}">关键词：</label>
							<div class="col-lg-6 col-sm-4">
								<input type="text" class="form-control" id="menu_content_click_{$i}_{$j}" name="click[{$i}][]" 
									value="<?=(isset($v['type']) && $v['type']=='click' && isset($v["key"]))?$v["key"]:(isset($form["click"][$i][$j])?$form["click"][$i][$j]:'')?>"/>
							</div>
							{$form_error.click[$i][$j]??''}
						</div>
						<div class="form-group" id="menu_content_border_view_{$i}_{$j}" <?=((isset($v['type']) && $v['type']=='view') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='view'))?'':'style="display:none"'?>>
							<label for="menu_content_view_{$i}_{$j}" class="control-label col-lg-2 col-sm-4">网址：</label>
							<div class="col-lg-6 col-sm-4">
								<input type="text" class="form-control" name="view[{$i}][]" id="menu_content_view_{$i}_{$j}" 
									value="<?=(isset($v['type']) && $v['type']=='view' && isset($v['url']))?$v['url']:(isset($form["view"][$i][$j])?$form["view"][$i][$j]:'')?>"/>
							</div>
							{$form_error.view[$i][$j]??''}
						</div>
						<div class="form-group" id="menu_content_border_scancode_push_{$i}_{$j}" <?=((isset($v['type']) && $v['type']=='scancode_push') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='scancode_push'))?'':'style="display:none"'?>>
							<label for="menu_content_scancode_push_{$i}_{$j}" class="control-label col-lg-2 col-sm-4">扫码推事件：</label>
							<div class="col-lg-6 col-sm-4">
								<input name="scancode_push[{$i}][]" class="form-control" id="menu_content_scancode_push_{$i}_{$j}" type="text" 
									value="<?=(isset($v['type']) && $v['type']=='scancode_push' && isset($v['key']))?$v['key']:(isset($form["scancode_push"][$i][$j])?$form["scancode_push"][$i][$j]:'');?>"/>
							</div>
							{$form_error.scancode_push[$i][$j]??''}
						</div>
						<div class="form-group" id="menu_content_border_scancode_waitmsg_{$i}_{$j}" <?=((isset($v['type']) && $v['type']=="scancode_waitmsg") || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='scancode_waitmsg'))?'':'style="display:none"'?>>
							<label for="menu_content_scancode_waitmsg_{$i}_{$j}" class="control-label col-lg-2 col-sm-4">扫码带提示：</label>
							<div class="col-lg-6 col-sm-4">
								<input name="scancode_waitmsg[{$i}][]" class="form-control" id="menu_content_scancode_waitmsg_{$i}_{$j}" type="text" 
									value="<?=(isset($v['type']) && $v['type']=='scancode_waitmsg' && isset($v['key']))?$v['key']:(isset($form["scancode_waitmsg"][$i][$j])?$form["scancode_waitmsg"][$i][$j]:'')?>"/>
							</div>
							{$form_error.scancode_waitmsg[$i][$j]??''}
						</div>
						<div class="form-group" id="menu_content_border_pic_sysphoto_{$i}_{$j}" <?=((isset($v['type']) && $v['type']=="pic_sysphoto") || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='pic_sysphoto'))?'':'style="display:none"'?>>
							<label for="menu_content_pic_sysphoto_{$i}_{$j}" class="control-label col-lg-2 col-sm-4">发图：</label>
							<div class="col-lg-6 col-sm-4">
								<input name="pic_sysphoto[{$i}][]" class="form-control" id="menu_content_pic_sysphoto_{$i}_{$j}" type="text" 
									value="<?=(isset($v['type']) && $v['type']=='pic_sysphoto' && isset($v['key']))?$v['key']:(isset($form["pic_sysphoto"][$i][$j])?$form["pic_sysphoto"][$i][$j]:'')?>"/>
							</div>
							{$form_error.pic_sysphoto[$i][$j]??''}
						</div>
						<div class="form-group" id="menu_content_border_pic_photo_or_album_{$i}_{$j}" <?=((isset($v['type']) && $v['type']=='pic_photo_or_album') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='pic_photo_or_album'))?'':'style="display:none"'?>>
							<label for="menu_content_pic_photo_or_album_{$i}_{$j}" class="control-label col-lg-2 col-sm-4">拍照或者相册发图：</label>
							<div class="col-lg-6 col-sm-4">
								<input name="pic_photo_or_album[{$i}][]" class="form-control" id="menu_content_pic_photo_or_album_{$i}_{$j}" type="text" 
									value="<?=(isset($v['type']) && $v['type']=='pic_photo_or_album' && isset($v['key']))?$v['key']:(isset($form["pic_photo_or_album"][$i][$j])?$form["pic_photo_or_album"][$i][$j]:'')?>"/>
							</div>
							{$form_error.pic_photo_or_album[$i][$j]??''}
						</div>
						<div class="form-group" id="menu_content_border_pic_weixin_{$i}_{$j}" <?=((isset($v['type']) && $v['type']=='pic_weixin') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='pic_weixin'))?'':'style="display:none"'?>>
							<label for="menu_content_pic_weixin_{$i}_{$j}" class="control-label col-lg-2 col-sm-4">微信相册发图：</label>
							<div class="col-lg-6 col-sm-4">
								<input name="pic_weixin[{$i}][]" class="form-control" id="menu_content_pic_weixin_{$i}_{$j}" type="text" 
									value="<?=(isset($v['type']) && $v['type']=='pic_weixin' && isset($v['key']))?$v['key']:(isset($form["pic_weixin"][$i][$j])?$form["pic_weixin"][$i][$j]:'')?>"/>
							</div>
							{$form_error.pic_weixin[$i][$j]??''}
						</div>
						<div class="form-group" id="menu_content_border_location_select_{$i}_{$j}" <?=((isset($v['type']) && $v['type']=='location_select') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='location_select'))?'':'style="display:none"'?>>
							<label for="menu_content_location_select_{$i}_{$j}" class="control-label col-lg-2 col-sm-4">发送位置：</label>
							<div class="col-lg-6 col-sm-4">
								<input name="location_select[{$i}][]" class="form-control" id="menu_content_location_select_{$i}_{$j}" type="text" 
									value="<?=(isset($v['type']) && $v['type']=='location_select' && isset($v['key']))?$v['key']:(isset($form["location_select"][$i][$j])?$form["location_select"][$i][$j]:'')?>"/>
							</div>
							{$form_error.location_select[$i][$j]??''}
						</div>
						<div class="form-group" id="menu_content_border_turn_{$i}_{$j}" <?=((isset($v['type']) && $v['type']=='turn') || (isset($form["type"][$i][$j]) && $form["type"][$i][$j]=='turn'))?'':'style="display:none"'?>>
							<label for="menu_content_turn_{$i}_{$j}" class="control-label col-lg-2 col-sm-4">顺序：</label>
							<div class="col-lg-6 col-sm-4">
								<input type="text" name="turn[{$i}][]" disabled="disabled" class="form-control" id="menu_content_turn_{$i}_{$j}" 
									value="<?=(isset($v['type']) && isset($v['turn']) && $v['turn'])?$v['turn']:(isset($form["turn"][$i][$j])?$form["turn"][$i][$j]:'')?>"/>
							</div>
							{$form_error.turn[$i][$j]??''}
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2 col-sm-4">编号：</label>
							<div class="col-lg-6 col-sm-4">
								<p class="form-control-static">{$i}_{$j}</p>
							</div>
							<div class="col-lg-4 col-sm-4"></div>
						</div>
						
					</div>
				{/for}
			{/for}
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 text-center">
			<input type="button" class="btn btn-success" value="保存" onclick="editorModal('确定要提交吗？');" data-toggle="modal" data-target="#myModal">
			<input type="button" class="btn btn-success" value="保存并上传" onclick="editorInput('saveAndUpdate','moreOperation');editorModal('确定要提交吗？');" data-toggle="modal" data-target="#myModal">
		</div>
	</div>
</form>
</div>
<script>
function changeTitle(obj){
	$('#menu_title_'+$(obj).attr('alt')).html($(obj).val());
}
function showMenu(obj){
	$("div[id^='menu_content_main_border_"+$(obj).attr('alt')+"'] div[id^='menu_content_border_']").hide();
	$('#menu_content_border_'+$(obj).val()+'_'+$(obj).attr('alt')).show();
}
function showMenu2(obj){
	$("div[id^='menu_content_main_border_']").hide();
	$('#menu_content_main_border_'+$(obj).attr('alt')).show();
}
function addMenu(){
	var i=1;
	for(;i<3;){
		if($('#menu_'+i+'_0').is(":hidden"))break;
		
		i++
	}
	$('#menu_'+i+'_0').show();
}
function viewMenu(site){
	var i=1;
	for(;i<5;){
		if($('#menu_'+site+'_'+i).is(":hidden"))break;
		i++
	}
	setDisabled(site+'_'+i,false);
	$("div[id^='menu_content_main_border_']").hide();
	$("#menu_content_main_border_"+site+'_'+i).show();
	$('#menu_'+site+'_'+i).show();
}

function deleteMenu(site){
	$('#menu_content_name_'+site).val(null);
	$('#menu_title_'+site).html('请重新添加');
	var a=$('#menu_content_type_'+site).val();
	$('#menu_content_'+a+'_'+site).val(null);
	$('#menu_'+site).hide();
	$('#menu_content_main_border_'+site).hide();
	setDisabled(site,'disabled');
};
function setDisabled(site,v){
	$('#menu_content_type_'+site).attr("disabled",v);
	$('#menu_content_name_'+site).attr("disabled",v);
	$('#menu_content_turn_'+site).attr("disabled",v);
	$('#menu_content_location_select_'+site).attr("disabled",v);
	$('#menu_content_pic_weixin_'+site).attr("disabled",v);
	$('#menu_content_pic_photo_or_album_'+site).attr("disabled",v);
	$('#menu_content_pic_sysphoto_'+site).attr("disabled",v);
	$('#menu_content_scancode_waitmsg_'+site).attr("disabled",v);
	$('#menu_content_scancode_push_'+site).attr("disabled",v);
	$('#menu_content_view_'+site).attr("disabled",v);
	$('#menu_content_click_'+site).attr("disabled",v);
}
</script>