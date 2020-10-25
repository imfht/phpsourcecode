<h2>全部支付方式：</h2>
<hr class="mb10"></hr>

<div class="list_b">
		<table width="100%">
			<tr>
				<th width="300">支付类型</th>
				<th>支付描述</th>
				<th width="100">管理操作</th>
			</tr>
			{loop $paymenteraddlist $vo}
			<tr>
				<td><img src="__APPURL__/images/{$vo['paytype']}.gif">{$vo['payname']}</a></td>
				<td>{$vo['description']}</td>
				<td>
				<a href="{url('index/paymenteradd',array('paytype'=>$vo['paytype']))}" class='button'><i class="fa fa-edit fa-lg"></i> 添加</a></td>
			</tr>
			{/loop}
		</table>
</div>