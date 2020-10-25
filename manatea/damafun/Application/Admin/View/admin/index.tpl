<{include file="public/header.tpl"}>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>管理员信息</h3>
<table class="table">
	<th>管理员ID</th>
	<th>管理员</th>
	<th>操作</th>
	<{foreach from="$data" item=row}>
	<tr>
		<td><{$row.id}></td>
		<td><{$row.name}></td>
		<td><a href="<{$smarty.const.__CONTROLLER__}>/delete/id/<{$row.id}>" onclick="return confirm('确定要删除吗？')">删除</a></td>
	</tr>
	<{/foreach}>
</table>
</div>
</div>
<{include file="public/footer.tpl"}>