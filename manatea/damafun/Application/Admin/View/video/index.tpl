<{include file="public/header.tpl"}>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>编辑视频</h3>
<table class="table">
	<th></th>
	<th>视频名</th>
	<th>上传时间</th>
	<th>上传用户</th>
	<th>视频类型</th>
	<th>点击量</th>
	<th>评论数量</th>
	<th colspan="2">操作</th>	

	<{foreach from="$data" item=row}>
		<tr>
			<td><img src="<{$smarty.const.APP_RES}>/uploads/images/<{$row.pic}>"></td>
			<td><{$row.name}></td>
			<td><{$row.ptime|date_format:"%Y-%m-%d %H:%M:%S"}></td>
			<td><{$row.uname}></td>
			<td><{$row.pname}></td>
			<td><{$row.hot}></td>
			<td><{$row.comnumber}></td>
			<td><a href="<{$smarty.const.__CONTROLLER__}>/mod/id/<{$row.id}>">修改</a></td>
			<td><a href="<{$smarty.const.__CONTROLLER__}>/comment/id/<{$row.id}>">查看评论</a></td>
			<td><a href="<{$smarty.const.__CONTROLLER__}>/dama/id/<{$row.id}>">弹幕管理</a></td>
			<td><a onclick="return confirm('你确定要删除该视频吗？')" href="<{$smarty.const.__CONTROLLER__}>/delete/id/<{$row.id}>">删除</a></td>
			
		</tr>
	<{/foreach}>
</table>
</div>
</div>
<{include file="public/footer.tpl"}>