<{include file="public/header.tpl"}>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>视频信息</h3>
<table class="table">
	<th></th>
	<th>视频名</th>
	<th>上传时间</th>
	<th>视频类型</th>
	<th>点击量</th>
	<th>评论数量</th>	
		<tr>
			<td><img src="<{$smarty.const.APP_RES}>/uploads/images/<{$video.pic}>"></td>
			<td><{$video.name}></td>
			<td><{$video.ptime|date_format:"%Y-%m-%d %H:%M:%S"}></td>
			<td><{$cat.name}></td>
			<td><{$video.hot}></td>
			<td><{$video.comnumber}></td>
			
		</tr>
</table>
<h3>评论</h3>
<table class="table">
	<th>用户id</th>
	<th>用户名</th>
	<th>评论时间</th>
	<th></th>	
	<{foreach from = "$data" item = row}>
	<tr>
		<td><{$row.uid}></td>
		<td><a href="<{$smarty.const.__APP__}>/user/mod/id/<{$row.uid}>"><{$row.name}></a></td>
		<td><{$row.time|date_format:"%Y-%m-%d %H:%M:%S"}></td>
		<td></td>
	</tr>
	<tr><th colspan="4">评论：</td></tr>
	<tr>
		<td colspan="3"><{$row.comment}></td>
		<td><a href="<{$smarty.const.__CONTROLLER__}>/delcom/id/<{$row.id}>/vid/<{$video.id}>" onclick="return confirm('确认删除该条评论吗')">删除</a>
		</td>
	</tr>
	<{/foreach}>
</table>
</div>
</div>
<{include file="public/footer.tpl"}>