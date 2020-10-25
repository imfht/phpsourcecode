<?php defined('APP_PATH') OR exit('No direct script access allowed'); ?>

<div class="row" >
	<div class="col-lg-12">
		<h4>位置：{$title}</h4>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <!-- 头部 导航 -->
                <ul class="nav nav-pills">
                    <li <?php echo (!isset($panel) || $panel==0)?'class="active"':'';?>>
                        <a href="{$public??''}mass/index/?panel=0{$ecms_hashur['href']??''}&def={$def?? '1'}&order_by_time={$order_by_time??'desc'}">待群发列表</a>
                    </li><li {$panel==1?'class="active"':''}>
                        <a href="{$public??''}mass/index/index/?panel=1{$ecms_hashur['href']??''}&def={$def?? '1'}&order_by_time={$order_by_time??'desc'}">已群发列表</a>
                    </li><li {$panel==2?'class="active"':''}>
                        <a href="{$public??''}mass/index/index/?panel=2{$ecms_hashur['href']??''}&def={$def?? '1'}&order_by_time={$order_by_time??'desc'}">新增群发</a>
                    </li>
                    <?php if(isset($panel) && $panel==3){ ?>
                        <li class="active">
                            <a href="javascript:void(0);">修改群发</a>
                        </li>
                    <?php }?>
                </ul>
            </div>
        </div>
	</div>
</div>
<br>
<div class="row">
	<div class="col-lg-12">
		<form action="{$public}mass/index/{$action?$action:'add'}" class="form-inline" id="editor_form" class="form-inline">
			{$error_msg??''}
			{$ecms_hashur['form']??''}
			<input type="hidden" name="user_id" id="preview_user_id" value="">
			<input type="hidden" name="operation_form" id='operation_form' value="" />
			<input type="hidden" name="panel" id='panel' value="{$panel??''}" />
			{/* name=id 用于群发编辑页，识别新增或修改的标识，同时是定位修改图文的标记 */}
			<input type="hidden" name="id" id="operation_id" value="{$id??''}" />
			<input type="hidden" name="site" id="operation_site" value="" />
            <input type="hidden" name="editor_type" id="editor_type" value="">
            <input type="hidden" name="msg_type" id="msg_type" value="{$msg_type?$msg_type:(isset($form['msg_type'])?$form['msg_type']:'text')}">
			<?php if(isset($panel) && ($panel==2 || $panel==3)){ //修改或新增  ?>
				<input type="hidden" name="is_ok" id="is_ok" value="{$is_ok??''}" />
				<div class="row">
					<div class="col-lg-12">
						<article id="limit_introduction">群发对象、性别、地区自定义尚待进一步开发，选择后无任何效果。‘开启自动’需要服务器支持并依据文档设置。默认时间为明日的当前时间</article>
						<table class="table table-bordered table-hover text-left">
							<tr>
								<td>群发对象：<select name="group" class="form-control">
										<option value="all">全部</option>
									</select>
								</td>
								<td>性别：<select name="sex" disabled class="form-control">
										<option value="0">全部</option>
										<option value="1">男</option>
										<option value="2">女</option>
									</select>
								</td>
								<td>地区：<select name="area" class="form-control">
										<option value="0">全部</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<span data-toggle="tooltip" title="该功能正在开发中，且需要设置服务器，部分主机（如虚拟机）可能不支持" class="text-primary">开启自动</span>：
									<select name="is_auto" class="form-control">
										<!-- <option value="1">是</option> -->
										<option value="0">否</option>
									</select>
								</td>
								<td>群发时间:
									<input type="datetime-local" name="send_time" class="form-control" 
										value="<?php echo (isset($send_time) && !empty($send_time))?date('Y-m-d\\TH:i',strtotime($send_time)):date('Y-m-d\\TH:i',time()+86400);?>"/>
								</td>
								<td>
								</td>
							</tr>
						</table>
						<div class="row">
							<div class="col-lg-12">
								<input type="button" class="btn btn-success" value="保存为草稿" data-toggle="modal"
									data-target="#myModal" 
									onClick="editorInput('addMass','editor_type');editorInput('0','is_ok');editorInput('editor_form','operation_form');editorModal('确定要保存吗？')"/>
								<input type="button" class="btn btn-success" value="保存" data-toggle="modal"
									data-target="#myModal" 
									onClick="editorInput('save','editor_type');editorInput('1','is_ok');editorInput('editor_form','operation_form');editorModal('确定要保存吗？')"/>
								<input name="saveAndDo" class="btn btn-success" type="submit" value="保存并群发" />
								<input type="button" class="btn btn-success" title="自动保存为草稿" value="预览(自动存草稿)"
									onClick="editorInput('preview','editor_type');editorInput('0','is_ok');doAjax();"/>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-lg-12">
						<ul class="nav nav-tabs" id="massContent" style="cursor: pointer;">
							<li <?=(!isset($msg_type) || $msg_type=='text')?'class="active"':'';?>><a href="#content_1" onclick="viewReply('text','reply');selectMass(this);editorInput('text','msg_type');">文本</a></li>
	                        <li {$msg_type=='img'?'class="active"':''}><a onclick="viewReply('img','reply');selectMass(this);editorInput('img','msg_type');">图片</a></li>
	                        <li {$msg_type=='voice'?'class="active"':''}><a onclick="viewReply('voice','reply');selectMass(this);editorInput('voice','msg_type');">语音</a></li>
	                        <li {$msg_type=='video'?'class="active"':''}><a onclick="viewReply('video','reply');selectMass(this);editorInput('video','msg_type');">视频</a></li>
	                        <li {$msg_type=='news'?'class="active"':''}><a onclick="viewReply('news','reply');selectMass(this);editorInput('news','msg_type');">图文</a></li>
						</ul>
						<div id="select" style="display:none;">
							<div id="select_area_file">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12" style="padding-top:10px;">
						<div id="select_content">
							{$reply_content??''}
						</div>
					</div>
				</div>
			<?php }else{ //群发列表 ?>
				<table class="table table-bordered table-hover text-center">
					<input type="hidden" name="is_do" value="{$is_do??''}" />
			        <tr align="left">
						<td colspan='13'>&nbsp;&nbsp;{$panel==1?'待群发列表':'已群发列表'}</td>
					</tr>
					<tr align="left">
						<td colspan="7">&nbsp;排序：
							<input type="hidden" name="order_by_time" id="order_by_time" value="{$order_by_time??'desc'}">
							<input type="button" value="时间{$order_by_time=='asc'?'升序':'降序'}" class="btn btn-{$order_by_time=='asc'?'primary':'info'}" 
								onClick="switchOrder('{$order_by_time=='asc'?'desc':'asc'}','order_by_time');editorInput('freshen','editor_type');this.form.submit();" 
								title="点击转换排序方式">
						</td>
						<td colspan="6">&nbsp;<input name="def" type="checkbox" id="def" onClick="editorInput('freshen','editor_type');this.form.submit()" value="1" {$def?"checked='checked'":''} ><label for="def">只看默认公众号</label>
				  </tr>
					<tr>
						<td width="4%">选择</td>
						<td width="2%">序号</td>
						<td width="4%">公众号</td>
						<td width="10%">预定时间</td>
						<td width="10%">执行时间</td>
						<td width="6%">群发类型</td>
						<td width="20%">群发内容</td>
						<td width="4%">
							<a href="javascrip:void(0)" data-toggle="tooltip" title="正在开发中，该功能需设置服务器">自动</a>
						</td>
						<td width="5%">开启</td>
						<td><span class="text-primary" data-toggle="tooltip" title="文本无需上传">操作</span></td>
					</tr>
					{volist name="list" id="mass" key="i"}
						<tr>
							<td>
								<input type="checkbox" value="{$i}" name='ids[]' <?=$aid!=$mass['aid']?'disabled="disabled"':''?>/>
								<input type="hidden" value="<?=$mass['id']?>" name='id[]' <?=$aid!=$mass['aid']?'disabled="disabled"':''?>/>
							</td>
							<td>{$i}</td>
							<td><?=$mass['aid']?></td>
							
							<td>{$mass.send_time??''}</td>
							<td><?=$mass['is_do']==1?$mass['do_send_time']:'未执行';?></td>
							<td>
		                        {$mass['msg_type']??''}
		                    </td>
							<td><?php 
		                            switch($mass['msg_type']){
		                                case '文本':
		                                	echo $mass['text'];
		                                break;
		                                case '图片': 
		                        ?>
		                                	<a href="{$mass['img']['img_url']??''}" target="_blank" title="标题：{$mass['img']['title']??''}，点击可浏览原图">
		                                		<img src="{$mass['img']['img_url']??''}" class="img-responsive img-rounded" style="width:300px;max-height:250px;">
		                        			</a>
		                        <?php break; case '音频': ?>
		                                	<audio src="{$mass['voice']['voice_url']??''}" 
		                                		preload="none" controls
		                                		title="{$mass['voice']['title']??''}">您的浏览器不支持audio标签，无法预览</audio>
		                        <?php break; case '视频': ?>
		                                	<video src="{$mass['video']['video_url']??''}" poster="{$mass['video']['thumb']??''}"
		                                		preload="none" controls style="max-width: 300px"
		                                		title="{$mass['video']['title']??''}">您的浏览器不支持video标签，无法预览</video>
		                        <?php   break;
		                                case '图文':
		                                	//$allNews=$mass['news'];
		                                	$a='';
		                                	foreach ($mass['news'] as $k =>$news){
		                                		$news['id']=isset($news['id'])?$news['id']:0;
		                                		$news['title']=isset($news['title'])?$news['title']:0;
		                                		$a.="<a 
		                                			href=\"http://".$_SERVER['HTTP_HOST'].$public."../view.php/?type=news&id=".$news['id']."\" 
		                                			target=\"_blank\" data-toggle=tooltip
		                                			title=\"点击预览，标题：".$news['title']."\">".$news['id']."</a>,";
		                                	}
		                                	$a=rtrim($a,',');
		                                	echo $a;
		                                break;
		                                default:
		                                	echo '未知内容';
		                            }
		                        ?>
		                    </td>
							<td><?=$mass['is_auto']==1?'是':'否';?></td>
							<td><?=$mass['is_ok']==1?'开启':'草稿'?></td>
							<td>
								
								<input type="button" class="btn btn-primary" value="<?=$mass['media_id']?'更新':'上传'?>" 
									onclick="editorInput({$i},'operation_site');editorModal('您确定要 &lt;font style=&quot;color:red&quot;&gt; 上传 &lt;/font&gt;吗？');
									editorInput('editor_form','operation_form');editorInput(<?=$mass['media_id']?"'update_to_wx'":"'up_to_wx'";?>,'editor_type');" 
									data-toggle="modal" data-target="#myModal" <?=($aid!=$mass['aid'] || $mass['is_do']==1 || $mass['msg_type']=='文本')?'disabled="disabled"':''?>/>
								<?php if($mass['is_do']!=1) { ?>
		                            <input type="button" class="btn btn-info" name="ready_preview" value="预览" 
		                            	onclick="editorInput({$i},'operation_site');editorInput('doPreview','editor_type');list_preview();" <?=($mass['is_do']==1 || $aid!=$mass['aid'])?'disabled="disabled"':''?>/>
		                        <?php } ?>
								<input type="button" class="btn btn-success" value="立即群发" 
									onclick="editorInput( {$i} ,'operation_site');editorModal('您确定要立即群发？');
									editorInput('editor_form','operation_form');editorInput('nowDoMass','editor_type');" 
									data-toggle="modal" data-target="#myModal" <?=($aid!=$mass['aid'] || $mass['is_do']==1)?'disabled="disabled"':''?>/>
								<a href="{php} if($aid==$mass['aid'] && $mass['is_do']!=1){ {/php}
					            			{$public}mass/index/toEditor?id={$mass['id']}{$ecms_hashur['href']??''}
					            		{php}}else{ {/php}
					            			javascript:void(0);
					            		{php}}{/php}" <?=($mass['is_do']==1 || $aid!=$mass['aid'])?'disabled="disabled"':''?>
									class="btn btn-warning">修改</a>
								<input type="button" class="btn btn-danger" value="删除" 
									onclick="editorInput(<?=$i?>,'operation_site');editorInput('editor_form','operation_form');
									editorModal('您确定要残忍删除？<br>该操作极有可能无法恢复哦！');editorInput('oneDelete','editor_type');" 
									data-toggle="modal" data-target="#myModal" <?=($mass['is_do']==1 || $aid!=$mass['aid'])?'disabled="disabled"':''?>/>
								
		                        
								    <input type="button" class="btn btn-primary" value="更多" onclick="viewMore({$mass.id??''},'',this)"/>
							</td>
						</tr>
						<tr style="display:none" id="more_{$mass.id??''}">
							<td colspan="10">
								<span>对象组别：</span>
								<span class="text-danger"><?=$mass['group']=='all'?'全部':$mass['group'];?></span>&nbsp;
								<span>性别：</span>
								<span class="text-danger"><?=$mass['sex']==0?'全部':$mass['sex'];?></span>&nbsp;
								<span>地区：</span>
								<span class="text-danger"><?=$mass['area']==0?'全部':$mass['area']?></span>
								<?php if($mass['is_do']==1){ ?>
									<br>
									群发状态：<?=$mass['msg_status']=="send success"?'成功':$mass['msg_status'];?> 
									原计划总人数：{$mass.total_count??''} 
									允许发送总人数：{$mass.filter_count??''}  
									成功人数：{$mass.sent_count??''}  
									失败人数：{$mass.error_count??''} 
									<br>
									<span class="" style="color:#514F4F">由于微信过滤机制，成功+失败&lt;=允许发送总人数&lt;=原计划总人数</span>
								<?php }?>
							</td>
						</tr>
						
					{/volist}
					<tr>
						<td height="35px"><input type="checkbox" id="choseAll" onclick="checkall(form,'ids','all')" name="all"/><br />全选</td>
						<td colspan="12"><input type="button" class="btn btn-danger" value="批量删除" onclick="editorInput('sDelete','editor_type');editorInput('editor_form','operation_form');editorModal('确定要残忍批量删除吗？<br>该操作极可能无法恢复哦！');" data-toggle="modal" data-target="#myModal"/></td>
					</tr>
	                <?php if(isset($page)){ ?>
	                    <tr class="text-left">
	                        <td colspan="13">
	                            <?=$page;?>
	                        </td>
	                    </tr>
	                <?php }?>
				</table>
			<?php }?>
			<?php if(!isset($mass['is_do']) || $mass['is_do']!=1){ ?>
				<div id="select_cover2" style="display:none;" onclick="backToPreview('select_cover2')">
					<div style="" id="select_area2">
						<span class="span1"><font color="#FFFFFF">个人微信号：</font><input name="preUser" type="text" id="preuser" /></span>
						<span class="span1"><input name="make_preview" type="submit" value="发送" onclick="return makePreview('preuser',this.name);"/></span>
						<span><input name="cancel" type="button" value="返回" onclick="return backToPreview('select_cover2');"/></span>
					</div>
					<div id="callback2" style="display:none">
						<span class="back_span"></span>
					</div>
				</div>
			<?php } ?>
		</form>
	</div>
</div>
<script type="text/javascript">
var strings='';
strings+='<div class="col-lg-8 col-md-12 form-inline" role="form"><div class="form-group">';
strings+='<input type="text" name="preview_user_id_linshi" class="form-control" id="preview_user_id_linshi" value="">';
strings+='</div><div class="form-group">';
strings+='&nbsp;&nbsp;<input type="button" class="form-control btn btn-success" name="submit" value="发送预览" onclick="editorInput('+"'doPreview','editor_type');doPreview()"+'">';
strings+='</div></div>';
//排序开关
function switchOrder(val,id){
	var obj=$('#'+id);
	var v=obj.val();
	if(v==val){
		obj.val('');
	}else{
		obj.val(val);
	}
}

function doAjax(){
	var u="<?php echo url('mass/index/editor','',false).'?'.$ecms_hashur['href'];?>";
	var a=$('#editor_form').serialize();
	var strings='<h2 style="margin:0px auto;padding:90px;" class="text-center">正在加载……</h2>';
	$('#myModal2 .modal-content').html(strings);
	$('#myModal2').modal('show');
	$('#myModal2').on('shown.bs.modal', function () { //当模态框显示后再执行后续操作
		Ajax2(u,a);
	});
}
function Ajax2(u,a){
	$.ajax({
		url:u, // ---- url路径，根据需要写,
		type:'post',
		data:a,
		dataType: "json",
		timeout:300000,//5分钟
		beforeSend:function(XMLHTTPRequest){
			
		   //$("#loading").html("正在发送...");
		},
		success:function(r,textStatus){
			if(r.error===0){
				$('#operation_id').val(r.id);
				var s=$('#myModal2 .modal-content').html();
				strings=s.replace(/正在保存……/g,strings);
				$('#myModal2 .modal-content').html(strings);
				getUserId();
			}else{
				var errSring='<h2 style="margin:0px auto;padding:90px;" class="text-center">保存失败……错误原因：'+r.message+'</h2>';
				$('#myModal2 .modal-content').html(errSring);
			}
		},

		error:function(XMLHTTPRequest,textStatus,errorThrown){
			alert('获取数据错误；error状态文本值：'+textStatus+" 异常信息："+errorThrown);
			return false;
		}
	});
}
var cookie_name='<?php echo $_SERVER['HTTP_HOST'];?>'+'_preview_user_id';
function getUserId(){
	var v;
	v=getCookie(cookie_name);
	$('#preview_user_id_linshi').val(v);
}

function doPreview(){
	var v=$('#preview_user_id_linshi').val();
	setCookie(cookie_name,v);
	$('#preview_user_id').val(v);
	$('#editor_form').submit();
}
function viewMore(site,type,obj){
	type=type || '';
	if($(obj).val()=='更多'){
		$('#'+type+'more_'+site).show();
		$(obj).val('隐藏');
	}else{
		$('#'+type+'more_'+site).hide();
		$(obj).val('更多');
	}
}
function list_preview(){
	var h='<h2 style="margin:0px auto;padding:90px;" class="text-center">'+strings+'</h2>';
	$('#myModal2 .modal-content').html(h);
	$('#myModal2').modal('show');
	$('#myModal2').on('shown.bs.modal', function () { //当模态框显示后再执行后续操作
		getUserId();
	});
}
</script>