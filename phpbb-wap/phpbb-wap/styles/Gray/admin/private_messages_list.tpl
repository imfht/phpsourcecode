<script type="text/javascript">
/**
* @参数 id form的id
* @参数 name 标记的名称
* @参数 state 选择或反选
*/
function marklist(id, name, state)
{
	var parent = document.getElementById(id);
	
	if (!parent)
	{
		eval('parent = document.' + id);
	}

	if (!parent)
	{
		return;
	}

	var rb = parent.getElementsByTagName('input');
	
	for (var r = 0; r < rb.length; r++)
	{	
		if (rb[r].name.substr(0, name.length) == name)
		{
			rb[r].checked = state;
		}
	}
}
</script>
			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;私人消息</div>
				<p>在这里，您可以查看所有存储在数据库中的信息，这有助于您清理论坛中乱发消息的用户，选择【清空所有】按钮没有任何提示，且删除后不可恢复！</p>
				<p>当网站运行一段时间之后，随着用户发送信息的增加，信息会占用数据库的空间，建议定时清理这些信息！</p>
				<div class="title">私人消息列表</div>
				<form id="pm_list" action="{S_DELETE_ACTION}" method="post">
<!-- BEGIN pmrow -->
					<div class="{pmrow.ROW_CLASS} module">
						<div><input type="checkbox" name="pm_id_list[]" value="{pmrow.PM_ID}" />{pmrow.L_MUNBER}、<a href="{pmrow.U_VIEW_PM}">{pmrow.SUBJECT}</a></div>
					</div>
<!-- END pmrow -->
<!-- BEGIN hide -->
					<div>还没有私人信息</div>
<!-- END hide -->
<!-- BEGIN show -->
					<div class="center">
						<br />
						<div>
							<a class="button" href="#" onclick="marklist('pm_list', 'pm_id_list', true); return false;">选择全部</a>
							<a class="button" href="#" onclick="marklist('pm_list', 'pm_id_list', false); return false;">取消选择</a>
						</div>
						<br />
						<div>
							<input class="button" type="submit" value="删除选中" />
							<input class="button" type="submit" name="delete_all" value="清空所有" />
						</div>
						<br />
					</div>
<!-- END show -->
				</form>
				{PAGINATION}
			</div>