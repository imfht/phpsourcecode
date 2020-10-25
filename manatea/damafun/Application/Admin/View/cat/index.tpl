<{include file="public/header.tpl"}>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>修改分类</h3>
<table class="table">
	<th>类名
	</th>
	<th colspan="2">操作</th>

	<{foreach from="$select" item=row}>
		<tr>
			<td><{$row.name}></td>
			<td><a href="<{$smarty.const.__CONTROLLER__}>/mod/id/<{$row.id}>">修改</a></td>
			<td><a onclick="return confirm('你确定要删除分类吗？')" href="<{$smarty.const.__CONTROLLER__}>/delete/id/<{$row.id}>">删除</a></td>
		</tr>
	<{/foreach}>
</table>
</div>
</div>
<{include file="public/footer.tpl"}>