<{include file="public/header.tpl"}>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>用户权限管理</h3>
<table class="table">
	<th>用户ID</th>
	<th>用户名</th>
	<th>性别</th>
	<th>状态</th>
	<th>操作</th>

	<{foreach from="$user" item=row}>
		<tr>
			<td><{$row.id}></td>
			<td><{$row.name}></td>
			<td><{if $row.sex==1}>男<{else}>女<{/if}></td>
			<td><{if $row.allow==1}>正常<{else}>冻结<{/if}></td>
			<td><a href="<{$smarty.const.__CONTROLLER__}>/mod/id/<{$row.id}>">管理</a></td>
		</tr>
	<{/foreach}>
</table>
</div>
</div>
<{include file="public/footer.tpl"}>