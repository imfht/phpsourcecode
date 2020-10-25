<h2>账号列表：</h2>
<hr class="mb10"></hr>
<a href="{url('index/ppacountadd')}" class="button mb10"><i class="fa fa-plus"></i> 添加账号</a>
<div class="t-pages right">{$page}</div>

	<div class="list_b">
		<table width="100%">
			<tr>
				<th width="40">ID</th>
				<th>公众账号名称</th>
				<th width="100">账号类型</th>
				<th width="450">URL</th>
				<th width="40">Token</th>
				<th width="30">状态</th>
				<th width="120">创建时间</th>
				<th width="220">管理操作</th>
			</tr>
			{loop $pplist $vo}
			<tr>
				<td>{$vo['id']}</td>
				<td>{$vo['name']}</td>
				<td>{$vo['category']}</td>
				<td><?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?r=ppacount/core/'.$vo['category'];?>&hash={$vo['hash']}</td>
				<td>{$vo['token']}</td>
				<td>
					{if $vo['state'] == 0}<span style="color: #f63">停用</span>{else}启用{/if}
				</td>
				<td><?php echo date('Y-m-d H:i:s',$vo['createtime']);?></td>
				<td>
					<a href="{url('index/ppacountselect',array(id=>$vo['id']))}" class="button"><i class="fa fa-cog fa-lg fa-spin"></i> 配置</a>
					<a href="{url('index/ppacountedit',array(id=>$vo['id']))}" class="button"><i class="fa fa-edit fa-lg"></i> 修改</a>
					<a onclick="return confirm('确定要删除吗？')" href="{url('index/ppacountdel',array(id=>$vo['id']))}" class="button"><i class="fa fa-trash-o fa-lg"></i> 删除</a>
				</td>
			</tr>
			{/loop}
		</table>
	</div>