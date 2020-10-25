<h2>用户分组列表：</h2>
<hr class="mb10"></hr>
<a href="{url('index/groupaddedit')}" class="button mb10"><i class="fa fa-plus"></i> 添加分组</a>

<form method="post" action="" target="_self">
	<div class="list_b">
		<table width="100%">
			<tr>
				<th>分组名称</th>
				<th width="80">分组排序</th>
				<th width="180">管理操作</th>
			</tr>
			{loop $grouplist $vo}
			<tr>
				<td>{$vo['name']}</td>
				<td>{$vo['sort']}</td>
				<td>
					<a href="{url('index/groupaddedit',array(id=>$vo['id']))}" class='button'><i class="fa fa-edit fa-lg"></i> 修改</a>
					<a onclick="return confirm('确定要删除吗？')" href="{url('index/groupdel',array(id=>$vo['id']))}" class='button'><i class="fa fa-trash-o fa-lg"></i> 删除</a>
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