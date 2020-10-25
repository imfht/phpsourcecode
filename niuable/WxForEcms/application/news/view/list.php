<?php defined('APP_PATH') OR exit('No direct script access allowed'); ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
	<span class="sr-only">Close</span></button>
	<h4>位置：图文列表</h4>
</div>
<div class="row">
	<div class="col-lg-12">
		<form action='{$public}news/index/newsList' 'id="list_list_form" class="form-inline">
			{$ecms_hashur['form']??''}
			<input type="hidden" name="list_num" id='list_num' value="{$list_num??''}" />
			
			<table class="table table-bordered table-condensed table-hover text-center">
				
				<tr align="left">
					<td colspan="7">
						<div class="form-group">
							<input name="search" type="search" class="form-control"
								value="{$search??''}" placeholder="请输入搜索内容">
						</div>
						<div class="form-group">
							<input type="button" value="搜索" onclick="doSearch();"
								class="btn btn-warning form-control">&nbsp;
							<?php echo (isset($search) && (!empty($search) || $search===0))?'正在搜索：'.$search:'';?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="5%">序号</td>
					<td width="8%">ID</td>
					<td width="20%">标题</td>
					<td width="20%">封面</td>
					<td width="15%">时间</td>
					<td width="8%">用于公众号</td>
					<td width="">操作</td>
				</tr>
				{volist name="list" id="v" key="k" empty="
				<tr>
					<td colspan=7>暂无数据</td>
				</tr>
				"}
				<tr>
					<td>{$k}</td>
					<td><input type="hidden" name="newsTitle[]" id="listTitle_<?=$k?>"
						value="{$v['title']?$v['title']:''}"> <input type="hidden"
						name="abstract[]" id="listAbstract_<?=$k?>"
						value="{$v['abstract']?$v['abstract']:''}"> <input
						type="hidden" name="id[]" id="listId_<?=$k?>"
						value="{$v['id']??''}"> <a
						href="{$public}news.php/view?type=news&id=<?=$v['id']?>"
						target="_blank">{$v['id']??''}</a></td>
					<td style="word-break: break-all;">{$v['title']??''}</td>
					<td><img id="listTitleImg_<?=$k?>"
						style="max-width: 200px; max-height: 200px;"
						src="{$v['title_img']??''}"></td>
					<td>{$v['create_time']??''}</td>
					<td>{$v['aid']??''}</td>
					<td><input type="button" class="btn btn-success" value="选择"
						onclick="newsSelect(<?=$k?>,<?=isset($list_num)?$list_num:'';?>)">
					</td>
				</tr> 
				{/volist}
	
			<?php if(isset($page)){?>
					<tr align="left">
					<td colspan="7">
							<?php echo $page;?>			
						</td>
				</tr>
			<?php }?>		
			       
			</table>
		</form>
	</div>
</div>
<script>
function doSearch(){
	var u="<?php echo url('news/index/newsList','',false)?>";
	var a=$('form').serialize();
	Ajax(u,a);
}
function newsSelect(site,listNum){
	$('#myModal2').modal('hide');
	$("#title_img_"+listNum).attr('src',$("#listTitleImg_"+site).attr('src'));
	$("#title_"+listNum).html($("#listTitle_"+site).val());
	$("#news_id_"+listNum).val($("#listId_"+site).val());
	$("#abstract_"+listNum).html($("#listAbstract_"+site).val());
}
function Ajax(u,a){
	$.ajax({
		url:u,// ---- url路径，根据需要写,
		type:'post',
		data:a,
		//dataType: "json",
		timeout:300000,//5分钟
		beforeSend:function(XMLHTTPRequest){
		   //alert('远程调用开始...');
		   //$("#loading").html("正在发送...");
		},
		success:function(data,textStatus){
			editorModal2(data,'myModal2');
			limitAction();
		},

		 error:function(XMLHTTPRequest,textStatus,errorThrown){
			alert('获取数据错误；error状态文本值：'+textStatus+" 异常信息："+errorThrown);
			return false;
		}
	});
}
function limitAction(){
	$('form').submit(function(e){
		//return false;
		e.preventDefault();
		doSearch();
	});//必须追加该函数，否者，将会允许“Enter”键提交表单，导致模态框失效、显示错误
	$('.pagination a').click(function(){
		Ajax2($(this).attr('href'));
		return false;
	});
	$(function () { $("[data-toggle='tooltip']").tooltip(); });
}
function Ajax2(u){
	$.ajax({
		url:u,// ---- url路径，根据需要写,
		type:'get',
		timeout:300000,
		beforeSend:function(XMLHTTPRequest){
		   //alert('远程调用开始...');
		   //$("#loading").html("正在发送...");
		},
		success:function(data,textStatus){
			editorModal2(data,'myModal2');
			limitAction();
		},

		 error:function(XMLHTTPRequest,textStatus,errorThrown){
			alert('获取数据错误；error状态文本值：'+textStatus+" 异常信息："+errorThrown);
			return false;
		}
	});
}
$(function(){
	$('.pagination a').click(function(){
		Ajax2($(this).attr('href'));
		return false;
	});
});
</script>
