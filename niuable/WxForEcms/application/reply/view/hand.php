<?php defined('APP_PATH') OR exit('No direct script access allowed'); ?>
<div class="row" >
	<div class="col-lg-12">
		<h4>位置：{$title??''}</h4>
	</div>
</div>
<form action='{$public}reply/hand/action' id="do_form" class="form-inline" method='post' role="form">
	<input type="hidden" name="type" value="{$type??''}" />
	<input type="hidden" id="operation_form" name="operation_form" value="do_form" />
	{$ecms_hashur['form']??''}
	<input type='hidden' name="msg_id_for_user" id="msg_id_for_user" 
		value="{$msg['id']??''}" />
	<input type='hidden' name="object_id" id="object_id" 
		value="{$object_id??''}" />
	<!-- 头部 导航 -->
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group col-lg-1 col-xs-2">
				<label class="control-label text-left">回复粉丝：</label>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-3 col-xs-4">
				<h4>
					<img src="{$user['head_img_url']??''}" class="img-responsive img-rounded" style="max-width:200px; max-height: 100px;margin-top:3px;">
				</h4>	
			</div>
			<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
				<h4>
					昵称：{$user['nick_name']??''}
				</h4>	
				<h4>
					被回复消息id: {$msg['id']??''}
				</h4>
			</div>
		</div>
	</div>
	<br>
	<div class="row text-left">
		<div class="col-lg-12">
			<div class="form-group">
				<label for="jumpMenu" class="control-label">回复类型：</label>
				<select name="msg_type" id="jumpMenu" class="form-control" 
					onchange="viewReply(this.value,'reply')">
					<option value="text">文本</option>
					<option value="img" >图片</option>
					<option value="voice" >语音</option>
					<option value="video" >视频</option>
					<option value="news" >图文</option>	
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div style="padding:10px 0;">
			{$reply_content??''}
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 text-center" style="padding:20px 0">
			<div class="form-group">
				<input type="reset" class="btn btn-primary form-control" id="button" 
					onclick="viewReply('text','reply')" value="重置" />
			</div>
			<div class="form-group">
				<input type="button" class="btn btn-success form-control" value="提交" 
					onclick="editorModal('确定这样回复吗？&lt;br&gt;&lt;font color=&quot;red&quot;&gt;请注意，该操作无法撤销，请谨慎操作！&lt;/font&gt;');" 
					data-toggle="modal" data-target="#myModal"/>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<table class="table table-bordered table-hover text-center">
				<tr style="text-align:left" class="active">
					<td colspan='8'>消息列表</td>
				</tr>
				<tr>
					<td width="3%">选择</td>
					<td width="6%">序号</td>
					<td width="6%">公众号id</td>
					<td width="10%">用户</td>
					<td width="25%">消息内容</td>
					<td width="6%">关键词</td>
					<td width="6%">已回复</td>
					<td>操作</td>
				</tr>
				{volist name="list.data" id="v" key="k" empty="<tr><td colspan=8>暂时没有数据</td></tr>" }
					<tr>
						<td>
							<input name="ids[]" id="ids" type="checkbox" value="{$k}" />
							<input name="id[]" type="hidden" value="{$v.id??''}" />
						</td>
						<td>{$v.id??''}</td>
						<td>{$v.aid??''}</td>
						<td style="padding: 0px auto;">
							<?php if (isset($v['is_reply'])) { ?>
								<h4>
									<img src="{$user['head_img_url']??''}" class="img-responsive img-rounded" 
									style="width:40px; height: 40px;margin:0 auto; margin-top:3px;">
								</h4>
								<h4>{$user['nick_name']?substr($user['nick_name'],0,8):''}</h4>
							<?php }else{ ?>
								<span class="text-center">我</span>
							<?php }?>
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
									<video style="max-width: 300px;max-height:300px" poster="{$v.thumb_media_url??''}" src="{$v.video_url??''}" preload="none" controls>您的浏览器不支持 video 标签。</video>
								</a>		
							<?php }elseif ($v['msg_type']=='shortvideo'){ ?>
								<a href="{$v.video_url??''}" target="_blank">
									<video style="max-width: 300px;max-height:300px" poster="{$v.thumb_media_url??''}" src="{$v.video_url??''}"  preload="none" controls>您的浏览器不支持 video 标签。</video>
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
						<td>{$v.is_keyword?'是':'否'}</td>
						<td>{$v.is_reply?'是':'否'}</td>
						<td>
							<a href="<?=$v['aid']!=$aid?'javascript:void(0)':url('reply/hand/index','',false).'?type=msg&id='.$v['id'].$ecms_hashur['href']?>" class="btn btn-success">回复</a>
							<input type="button" class="btn btn-info" value="更多" onclick="viewMore('{$k}','msg_',this);">
						</td>
					</tr>
					<!-- 更多 -->
					<tr id="msg_more_{$k}" style="display: none;">
						<td colspan="8">
							<span>消息类型：
								<?php 
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
							<a href="<?=(isset($v['image_url']) && $v['image_url'])?$v['image_url']:((isset($v['video_url']) && $v['video_url'])?$v['video_url']:(isset($v['voice_url']) && $v['voice_url']?$v['voice_url']:'javascript:void(0)'));?>" 
								target="_blank" class="btn btn-primary" <?=((isset($v['image_url']) && $v['image_url']) || (isset($v['voice_url']) && $v['voice_url']) || (isset($v['video_url']) && $v['video_url']))?'':'disabled';?>>下载</a>
							<h5>注意：超过3天的媒体文件（语音、视频、图片等）可能无法下载</h5>
							<h4>发送时间：<span style="color: red;">{$v.create_time??''}</span></h4>
							<h4>
								<span data-toggle="tooltip" title="如果关注时间晚于发送时间，可能是用户取消关注后重新关注">关注时间</span>：
								<span style="color: red;">{$user['subscribe_time']??''}</span>
							</h4>
							<h4>分组：<small>{$user['group_id']??''}</small></h4>
							<h4>区域：
								<span class="text-success">{$user['country']??''}</span>&nbsp;&nbsp;
								<span class="text-danger">{$user['province']??''}</span>&nbsp;&nbsp;
								<span class="text-info">{$user['city']??''}</span>
							</h4>
							<h4 class="text-success"><span class='text-primary'>语言：{$user['language']??''}</span></h4>
						</td>
					</tr>
					<!-- /更多 -->
					<?php 
						//公众号回复给用户的信息
						if(isset($v['is_reply']) && isset($v['msgreply']) && !empty($v['msgreply'])){
							foreach ($v['msgreply'] as $key => $msgreply){
					?>
								<tr>
									<td>{/* <input name="msgreply_ids[]" type="checkbox" value="" /> */}</td>
									<td><span style="color: red">--</span></td>
									<td>{$msgreply['aid']??''}</td>
									<td style="padding: 0px auto;">
										<span class="text-center">我</span>
									</td>
									<td>
										<?php if($msgreply['msg_type']=='text'){ ?>
											{$msgreply['text']??''}
										<?php }elseif ($msgreply['msg_type']=='img'){ ?>
											<a href="{$msgreply['img']['url']??''}" target="_blank">
												<img src="{$msgreply['img']['url']??''}" title="点击图片，可查看原图" 
													style="width:160px;max-height:100%;margin:0 auto;" class="img-responsive img-rounded" 
													id="content_img" onMouseMove="change(200,this);" onMouseOut="changeBack(this);" />
											</a>
										<?php } elseif ($msgreply['msg_type']=='voice'){ ?>
											<a href="{$msgreply['voice']['url']??''}" target="_blank">
												<audio src="{$msgreply['voice']['url']??''}" preload="none" controls>您的浏览器不支持 audio 标签。</audio>
											</a>
										<?php }	elseif ($msgreply['msg_type']=='video'){ ?>
										 	<a href="{$msgreply['video']['url']??''}" target="_blank">
												<video style="max-width: 300px;max-height:300px" src="{$msgreply['video']['url']??''}" poster="{$msgreply['video']['thumb']}" preload="none" controls>您的浏览器不支持 video 标签。</video>
											</a>
										<?php }	elseif ($msgreply['msg_type']=='shortvideo'){ ?>
											<a href="{$msgreply['videol']['url']??''}" target="_blank">
												<video style="max-width: 300px;max-height:300px" src="{$msgreply['video']['url']??''}" poster="{$msgreply['video']['thumb']}" preload="none" controls>您的浏览器不支持 video 标签。</video>
											</a>
										<?php }elseif ($msgreply['msg_type']=='location'){ ?>
											{$msgreply['label']??''}
										<?php }elseif ($msgreply['msg_type']=='link'){ ?>
											<a href="{$msgreply['url']??''}" target="_blank" title="{$msgreply['description']??''}" >
												{$msgreply['title']??''}
											</a>
										<?php }elseif ($msgreply['msg_type']=='news'){ 
										          $news = $msgreply['news'];
										          $num = count($news);
										          foreach ($news as $key => $item){
										              $isLast = $key+1 == $num;
										?>
											<a href='{$item['url']}' target="_blank" title="{$item['title']}">{$item['id']}</a>	{$isLast?'':','}
										<?php     
										          }
										      } 
										?>
									</td>
									<td>--</td>
									<td>--</td>
									<td>--</td>
								</tr>
								<!-- 更多 -->
								<tr id="msgreply_more_{$key}" style="display: none;">
									<td colspan="8">
										<span>消息类型：<?php 
												if($msgreply['msg_type']=='text') echo '文本';
												elseif ($msgreply['msg_type']=='img') echo '图片';
												elseif ($msgreply['msg_type']=='voice') echo '语音';
												elseif ($msgreply['msg_type']=='video') echo '视频';
												elseif ($msgreply['msg_type']=='news') echo '图文';
// 												elseif ($msgreply['msg_type']=='location') echo '地理位置';
// 												elseif ($msgreply['msg_type']=='link') echo '链接消息';
// 												elseif ($msgreply['msg_type']=='event'){
// 													if($msgreply['event']=='subscribe') echo '关注';
// 													elseif($msgreply['event']=='unsubscribe') echo '取消关注';
// 													elseif($msgreply['event']=='LOCATION') echo '上报地理位置';
// 													elseif($msgreply['event']=='CLICK') echo '点击菜单';
// 													elseif($msgreply['event']=='subscribe') echo '关注';
// 												}
											?>
										</span>
										<a href="<?=(isset($msgreply['img_url']) && $msgreply['img_url'])?$msgreply['img_url']:((isset($msgreply['video_url']) && $msgreply['video_url'])?$msgreply['video_url']:'javascript:void(0)');?>" 
											target="_blank" class="btn btn-primary" <?=((isset($msgreply['img_url']) && $msgreply['img_url']) || (isset($msgreply['video_url']) && $msgreply['video_url']))?'':'disabled'?>>下载</a>
										<h5>注意：超过3天的媒体文件（语音、视频、图片等）可能无法下载</h5>
										<h4>发送时间：<span style="color: red;">{$v['create_time']??''}</span></h4>
									</td>
								</tr>
								<!-- /更多 -->
					<?php 
							} 
						}
					?>
				{/volist}
				<tr>
		            <td>
			            <label><input type="checkbox" onclick="checkall(form, 'ids')" name="all" /><br />全选</label>
		            </td>
		            <td colspan="9">
						{$page??''}
				   </td>
			</table>
		</div>
	</div>	
	
	<div id="select_area" style="display:none;" tabindex="-1">
	    <div id="select_area_1">
	    </div>
	</div>
</form>
<script>
	function loadModal(id){
		$('#'+id).modal('show');
	}
	function More2(e){
		//alert(e.alt);
		$("#select_area_1").html("<iframe id='iframe_image' src='"+e.alt+"' ></iframe>");
		//$("#select_area_1").html="<iframe id='iframe_image' src='"+e.alt+"' ></iframe>";
		$("#select_area").show();
		alert(e.name);
	}
	function sure2(u){
		u = u || "<?php echo url('reply/hand/do','',false).'?'.$ecms_hashur['href']?>";
		a=$('#form').serialize();
		Ajax(u,a);
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
	function tPage(obj,form){
		var page=$(obj).attr('data-ci-pagination-page');
		$('#tpage').val(page);
		var u="<?php echo url('msg/index/index','',false).'?'.$ecms_hashur['href'];?>";
		form = form || 'list_form';
		$('#'+form).submit();
		//Ajax(u,a);
	}
</script>