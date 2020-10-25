<h2>已配置支付列表：</h2>
<hr class="mb10"></hr>
<a href="{url('index/paymenteraddlist')}" class="button mb10"><i class="fa fa-plus"></i> 添加支付</a>

<form method="post" action="" target="_self">
	<div class="list_b">
		<table width="100%">
			<tr>
				<th width="300">支付类型</th>
				<th>支付描述</th>
				<th width="40">状态</th>
				<th width="220">管理操作</th>
			</tr>
			{loop $paymenterlist $vo}
			<tr>
				<td><img src="__APPURL__/images/{$vo['paytype']}.gif"></a></td>
				<td>{$vo['description']}</td>
				<td>{if $vo['state'] == 1}启用{else}未启用{/if}</td>
				<td>
				<a href="{url('index/paymenteredit',array('id'=>$vo['id']))}" class='button'><i class="fa fa-edit fa-lg"></i> 修改</a>
				<a onclick="return confirm('确定要删除吗？')" href="{url('index/paymenterdel',array('id'=>$vo['id']))}" class='button'><i class="fa fa-trash-o fa-lg"></i> 删除</a></td>
			</tr>
			{/loop}
		</table>
	</div>
</form>

<script type="text/javascript">
Do.ready('base', function(){

});
</script>