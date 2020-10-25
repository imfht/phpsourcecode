<h2>数据列表：</h2>
<hr class="mb10"></hr>
<a href="{url('index/demoadd')}" class="button mb10"><i class="fa fa-plus"></i> 添加数据</a>

<form id="search" method="get" action="#">
	<div id="search_div">
		&nbsp;&nbsp;分类:
		<select id="s_cid" class="searchauto" name="s_cid">
			<option selected value="">选择分类</option>
			<option value="1">分类一</option>
			<option value="2">分类二</option>
		</select>
		
		&nbsp;&nbsp;状态:
		<select id="s_state" class="searchauto" name="s_state">
			<option selected value="">不限</option>
			<option value="1">显示</option>
			<option value="2">不显示</option>
		</select>
		
		&nbsp;&nbsp;关键字:
		<input id="s_keyword" class="input w150" type="text" name="s_keyword">
		<input class="button" value="搜 索" type="submit">
	</div>
</form>

<form method="post" action="" target="_self">
	<div class="list_b">
		<table width="100%">
			<tr>
				<th width="30"><input onclick="checkall(this)" type="checkbox"></th>
				<th width="60">id</th>
				<th>数据标题</th>
				<th width="40">分类</th>
				<th width="120">发布时间</th>
				<th width="220">管理操作</th>
			</tr>
			{loop $demolist $vo}
			<tr>
				<td><input id=id[] value=1501092 type=checkbox name=id[]></td>
				<td>{$vo['id']}</td>
				<td><a href="#" target="_blank">{$vo['title']}</a></td>
				<td>{$vo['categoryname']}</td>
				<td><?php echo date('Y-m-d H:i:s',$vo['createtime']);?></td>
				<td>
				<a href="{url('index/demoedit',array('id'=>$vo['id']))}" class='button'><i class="fa fa-edit fa-lg"></i> 修改</a>
				<a onclick="return confirm('确定要删除吗？')" href="{url('index/demodel',array('id'=>$vo['id']))}" class='button'><i class="fa fa-trash-o fa-lg"></i> 删除</a></td>
			</tr>
			{/loop}
		</table>
	</div>
	<div class="list_btn mt10">操作：
		<select id="mode" onchange="selectchange(this.value)" name="mode">
			<option selected value="1">彻底删除</option>
			<option value="2">批量操作状态</option>
		</select>
		<span id="sub4">
		  <select name="cid">
			<option value="2">转移栏目演示1</option>
			<option selected value="1">转移栏目演示2</option>
		  </select>
		</span>
		<input class="button" onclick="return confirm('确定操作吗？')" value="确 定" type="submit">
	</div>
</form>

<script type="text/javascript">
Do.ready('base', function(){

});
</script>