<?php defined('APP_PATH') OR exit('No direct script access allowed'); ?>

<div class="row" >
	<div class="col-lg-12">
		<h4>位置：消息管理</h4>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-info">
			<div class="panel-heading"><h4 class="panel-title">消息筛选</h4></div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="post" action="{$public}msg/index" id="list_form">
					{$ecms_hashur['form'] ?? ''}
					<input type="hidden" name="per_page" id="tpage">
					<table class="table table-hover table-bordered"> 
						<tr>
							<td>
								&nbsp;&nbsp;<input name="is_hide_keyword" type="checkbox" id="is_hide_keyword" value="1" 
									{$is_hide_keyword ? 'checked="checked"':''}><span> 隐藏关键词消息</span>
							</td>
							<td>	
								<span class="msg-search">&nbsp;&nbsp;
								&nbsp;<input type="checkbox" value="1" name="def" id="def" {$def ? 'checked="checked"':''}><span >只看默认公众号</span>
								<input type="button" class="btn btn-danger" value="清理旧消息" onClick="editorInput('clearOldMsg','editor_type');editorInput('editor_form','operation_form');
										editorModal('&lt;h4 class=text-danger&gt;确认要清理旧消息？&lt;br&gt;删除消息不可撤销，且极可能无法找回，请谨慎操作&lt;br&gt;&lt;br&gt;&lt;small&gt;该操作将清理5天前的消息，并保留收藏的星标消息&lt;br&gt;本页删除操作将同时删除关联的回复消息记录！&lt;/small&gt;&lt;/h4&gt;');"
									data-toggle="modal" data-target="#myModal"/>
						 		</span>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td colspan="3">
								<div class="msg-select">
									<input name="ttime" type="submit" class="btn btn-<?php echo (isset($time) && $time==0)?'success':'info';?>" value="全部消息">
									<input name="ttime" type="submit" class="btn btn-{$time==1?'success':'info'}" value="今天">
									<input name="ttime" type="submit" class="btn btn-{$time==2?'success':'info'}" value="昨天">
									<input name="ttime" type="submit" class="btn btn-{$time==3?'success':'info'}" value="前天">
									<input name="ttime" type="submit" class="btn btn-{$time==4?'success':'info'}" value="更早">
								</div>
							</td>
						</tr>
						<input type="hidden" name="time" id="time" value="{$time ?? ''}"/>
						<input type="hidden" name="isLock" id="isLock"/>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div>
			<form action="{$public??''}msg/index/editor" id="editor_form" method="post">
				{$ecms_hashur['form'] ?? ""}
				<input type="hidden" name="editor_type" id="editor_type" value="">
				<input type="hidden" name="operation_form" id='operation_form' value="" />
				<input type="hidden" name="site" id='site' value="" />
				<div id="msglist">
					<table class="table table-bordered table-hover text-center">
						<tr style="text-align:left" class="active">
							<td colspan='8'>消息列表</td>
						</tr>
						<tr>
							<td width="5%">选择</td>
							<td width="6%">序号</td>
							<td width="6%">公众号id</td>
							<td width="10%">用户</td>
							<td width="25%">消息内容</td>
							<td width="6%">关键词</td>
							<td width="6%">已回复</td>
							<td>操作</td>
						</tr>
						{volist name="list" id="v" key="k" empty="<tr><td colspan=8>暂时没有数据</td></tr>" }
							<tr>
								<td>
									<input name="ids[]" type="checkbox" value="{$k}" />
									<input name="id[]" type="hidden" value="{$v.id}" />
								</td>
								<td>{$v['id']}</td>
								<td>{$v.aid??''}</td>
								<td style="padding: 0px auto;">
									<h4>
										<img src="{$v['user']['head_img_url'] ?? ''}" class="img-responsive img-rounded" 
											style="width:40px; height: 40px;margin:0 auto; margin-top:3px;">
									</h4>
									<h4>{$v['user']['nick_name']?substr($v['user']['nick_name'],0,8):''}</h4>
								</td>
								<td>
									<?php if($v['msg_type']=='text'){ ?>
										{$v.content??''} 
									<?php }elseif($v['msg_type']=='img'){ ?>
										<a href="{$v.image_url??''}" target="_blank">
											<img src="{$v.image_url??''}" title="微信图片，点击可下载" alt="看到这句话，说明图片已过期"
												style="width:160px;max-height:100%; margin:0 auto;" id="content_img" class="img-responsive img-rounded" 
												onMouseMove="change(200,this);" onMouseOut="changeBack(this);">
										</a>
									<?php }elseif ($v['msg_type']=='voice'){?>
										<h4 class="text-danger">
											由于浏览器不支持amr格式音频，请下载后播放<br><br>
											点击“<span class="text-success">更多</span>”=》点击“<span class="text-success">下载</span>”
										</h4>
									<?php }elseif ($v['msg_type']=='video'){ ?>
										<a href="{$v.video_url??''}" target="_blank">
											<video src="{$v.video_url??''}" preload="none" controls>您的浏览器不支持 video 标签。</video>
										</a>		
									<?php }elseif ($v['msg_type']=='shortvideo'){ ?>
										<a href="{$v.video_url??''}" target="_blank">
											<video src="{$v.video_url??''}" preload="none" controls>您的浏览器不支持 video 标签。</video>
										</a>
									<?php }elseif ($v['msg_type']=='location'){ ?>
										{$v.label}
									<?php }elseif ($v['msg_type']=='link'){ ?>
										<a href="{$v.url??''}" target="_blank" title="{$v.description??''}">
											{$v.title}
										</a>
									<?php }elseif ($v['msg_type']=='event'){
											if($v['event']=='subscribe') echo '关注';
											elseif($v['event']=='unsubscribe') echo '取消关注';
											elseif($v['event']=='LOCATION') echo '上报地理位置';
											elseif($v['event']=='CLICK') echo '点击菜单';
											elseif($v['event']=='subscribe') echo '关注';
										}
									?>
								</td>
								<td>{$v.is_keyword ?'是':'否'}</td>
								<td>{$v.is_reply ?'是':'否'}</td>
								<td>
									<a href="<?=$v['aid']==$aid?$public.'reply/hand/index?type=msg&id='.$v['id'].$ecms_hashur['href']:'javascript:void(0)'?>" 
										class="btn btn-success {$v['aid']==$aid?'':'disabled'}">回复</a>
									<input type="button" class="btn btn-info" value="更多" onclick="viewMore('{$k}','list_',this);">
								</td>
							</tr>
							<!-- 更多 -->
							<tr id="list_more_{$k}" style="display: none;">
								<td colspan="8">
									<span>消息类型：<?php 
											if($v['msg_type']=='text') echo '文本';
											elseif ($v['msg_type']=='img') echo '图片';
											elseif ($v['msg_type']=='voice') echo '语音';
											elseif ($v['msg_type']=='video') echo '视频';
											elseif ($v['msg_type']=='shortvideo') echo '小视频';
											elseif ($v['msg_type']=='location') echo '地理位置';
											elseif ($v['msg_type']=='link') echo '链接消息';
											elseif ($v['msg_type']=='event'){
												if($v['event']=='subscribe') echo '关注';
												elseif($v['event']=='unsubscribe') echo '取消关注';
												elseif($v['event']=='LOCATION') echo '上报地理位置';
												elseif($v['event']=='CLICK') echo '点击菜单';
												elseif($v['event']=='subscribe') echo '关注';
											}
										?>
									</span>
									<a href="<?=(isset($v['image_url']) && $v['image_url'])?$v['image_url']:((isset($v['video_url']) && $v['video_url'])?$v['video_url']:((isset($v['voice_url']) && $v['voice_url'])?$v['voice_url']:'javascript:void(0)'));?>" 
										target="_blank" class="btn btn-primary" <?=((isset($v['image_url']) && $v['image_url']) || (isset($v['voice_url']) && $v['voice_url'])|| (isset($v['video_url']) && $v['video_url']))?'':'disabled';?>>下载</a>
									<h5>注意：超过3天的媒体文件（语音、视频、图片等）可能无法下载</h5>
									<h4>发送时间：<span style="color: red;">{$v.create_time??''}</span></h4>
									<h4>
										<span data-toggle="tooltip" title="如果关注时间晚于发送时间，可能是用户取消关注后重新关注">关注时间</span>：
										<span style="color: red;">{$v['user']['subscribe_time']??''}</span>
									</h4>
									<h4>分组：<small>{$v['user']['group_id']??''}</small></h4>
									<h4>区域：<span class="text-success" style="padding-right: 10px;">{$v['user']['country']??''}</span>
										<span class="text-danger" style="padding-right: 10px;">{$v['user']['province']??''}</span>
										<span class="text-info">{$v['user']['city']??''}</span>
									</h4>
									<h4 class="text-success">语言：<span class='text-primary'>{$v['user']['language']??''}</span></h4>
								</td>
							</tr>
							<!-- /更多 -->
						{/volist}
						<tr>
				            <td>
					            <label><input type="checkbox" onclick="checkall(form, 'ids')" name="all" /><br />全选</label>
				            </td>
				            <td colspan="9">
				            	<input type="button" class="btn btn-danger" onclick="editorInput('sDelete','editor_type');
				            		editorInput('editor_form','operation_form');
				            		editorModal('<span class=text-danger>确定要残忍 批量删除？<br>删除不能撤销，数据也极可能无法找回，请谨慎操作！</span>');"  
				            		value='删除所选' data-toggle="modal" data-target="#myModal"/>
				            </td>
				        </tr>
						{if condition="!empty($page)"}
							<tr>
								<td colspan="10">{$page}</td>
							</tr>
						{/if}
					</table>
				</div>
			</form>
		</div>
	</div>
</div>
<script>

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
function tPage(obj,form){
	var page=$(obj).attr('data-ci-pagination-page');
	$('#tpage').val(page);
	var u="<?php echo $public.'msg/index/index?'.$ecms_hashur['href'];?>";
	form = form || 'list_form';
	$('#'+form).submit();
	//Ajax(u,a);
}
</script>