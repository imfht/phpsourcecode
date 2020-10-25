<{include file="public/header.tpl"}>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>弹幕</h3>
<table class="table">
	<th>弹幕</th>
	<th>发送时间</th>
	<th>用户名</th>
	<th>操作</th>
	<{foreach from="$data" item=row}>
		<tr>
			<td><{$row[0]}></td>
			<td><{$row[1][0]}>s</td>
			<td><{$row[1][8]}></td>
			<td><a onclick="return confirm('你确定要删除该弹幕吗？')" href="<{$smarty.const.__CONTROLLER__}>/deldama/time/<{$row[1][0]}>/vid/<{$vid}>">删除弹幕</a></td>
		</tr>
	<{/foreach}>
</table>
</div>
</div>
<{include file="public/footer.tpl"}>