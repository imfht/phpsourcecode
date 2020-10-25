<h2>用户列表：</h2>
<hr class="mb10"></hr>

<form id="search" method="get" action="#">
	<div id="search_div">
		&nbsp;&nbsp;性别:
		<select id="s_sex" class="searchauto" name="sex">
			<option selected value="">全部</option>
			<option value="2">男</option>
			<option value="1">女</option>
		</select>
		
		&nbsp;&nbsp;姓名:
		<input id="s_name" class="input w150" type="text" name="s_name">
		
		&nbsp;&nbsp;手机号码:
		<input id="s_tel" class="input w150" type="text" name="s_tel">
		<input class="button" value="搜 索" type="submit">
	</div>
</form>

<form method="post" action="" target="_self">
	<div class="list_b">
		<table width="100%">
			<tr>
				<th width="60">姓名</th>
				<th width="60">手机号码</th>
				<th width="40">性别</th>
				<th width="60">状态</th>
				<th>地址</th>
				<th width="220">管理操作</th>
			</tr>
			{loop $userlist $vo}
			<tr>
				<td>{$vo['name']}</td>
				<td>{$vo['tel']}</td>
				<td>{$vo['sex']}</td>
				<td>{$vo['state']}</td>
				<td>{$vo['addr_prov']}---{$vo['addr_city']}---{$vo['addr_area']}---{$vo['address']}</td>
				<td>
				<a href="#" class='button'><i class="fa fa-cog fa-lg fa-spin"></i> 配置</a>
				<a href="#" class='button'><i class="fa fa-edit fa-lg"></i> 修改</a>
				<a onclick="return confirm('确定要删除吗？')" href="#" class='button'><i class="fa fa-trash-o fa-lg"></i> 删除</a></td>
			</tr>
			{/loop}
		</table>
	</div>
</form>

<script type="text/javascript">
function selectchange(id){
	if(id == '4'){
		$("#sub"+id).removeAttr("style");
	}else{
		$("#sub4").attr("style","display:none");
	}
}

Do.ready(function(){

});
</script>