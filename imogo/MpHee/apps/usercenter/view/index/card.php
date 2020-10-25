<h2>手机端显示配置列表：</h2>
<hr class="mb10"></hr>
<a href="{url('index/cardaddedit')}" class="button mb10"><i class="fa fa-plus"></i> 添加显示方案</a>

<form method="post" action="" target="_self">
	<div class="list_b">
		<table width="100%">
			<tr>
				<th>会员卡名称</th>
				<th width="80">电话</th>
				<th width="180">管理操作</th>
			</tr>
			{loop $cardlist $vo}
			<tr>
				<td>{$vo['cardname']}</td>
				<td>{$vo['cardphone']}</td>
				<td>
					<a href="{url('index/cardaddedit',array(id=>$vo['id']))}" class='button'><i class="fa fa-edit fa-lg"></i> 修改</a>
					<a onclick="return confirm('确定要删除吗？')" href="{url('index/carddel',array(id=>$vo['id']))}" class='button'><i class="fa fa-trash-o fa-lg"></i> 删除</a>
				</td>
			</tr>
			{/loop}
		</table>
	</div>
</form>

<script type="text/javascript">
Do.ready(function(){

});
</script>