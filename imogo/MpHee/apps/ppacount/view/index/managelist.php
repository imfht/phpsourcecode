<h2>分权账户列表：</h2>
<hr class="mb10"></hr>
<a href="{url('index/manageaddedit')}" class="button mb10"><i class="fa fa-plus"></i> 添加管理账户</a>

	<div class="list_b">
		<table width="50%">
			<tr>
				<th width="100">账户用户名</th>
				<th>管理账户和功能</th>
				<th width="120">备注信息</th>
				<th width="150">管理操作</th>
			</tr>
			{loop $managelist $vo}
			<tr>
				<td>{$vo['username']}</td>
				<td>
					<table id="actiontab{$vo['id']}">
						<div data='{$vo["manage"]}' class="managelist" tableid="{$vo['id']}" style="dispaly:none"></div>
					</table>
				</td>
				<td>{$vo['remark']}</td>
				<td>
				<a href="{url('index/manageaddedit',array('id'=>$vo['id']))}" class='button'><i class="fa fa-edit fa-lg"></i> 修改</a>
				<a onclick="return confirm('确定要删除吗？')" href="{url('index/managedel',array('id'=>$vo['id']))}" class='button'><i class="fa fa-trash-o fa-lg"></i> 删除</a></td>
			</tr>
			{/loop}
		</table>
	</div>

<script type="text/javascript">
function ppname(ppid){
	var pplist = <?php echo json_encode($pplist);?>;
	for(var i = 0;i < pplist.length;i++){
		if( pplist[i].id == ppid){
			return pplist[i].name;
		}
	}
}

function actionname(action){
	var actions = <?php echo json_encode($apps);?>;
	return actions[action].APP_NAME;
}

function managelist(data,id){
	var all = JSON.parse( data );
	
	for(var i = 0;i<all.length;i++){
		var text = '<tr><td width="150px">'+ppname( all[i].ppid )+'</td><td>';
		var actions = all[i].action;
		for(var j = 0;j<actions.length;j++){
			text += '<span class="mr10" data="'+actions[j]+'">'+actionname( actions[j] )+'</span>';
		}
		text += '</td></tr>';
		$('#actiontab'+id).append(text);
	}
}

Do.ready(function(){
	$(".managelist").each(function(){
		var tableid = $(this).attr('tableid');
		var managedata = $(this).attr('data');
		managelist(managedata,tableid);
	});
});
</script>