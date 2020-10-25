			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;所有附件</div>
				<form id="attach_list" method="post" name="attach_list" action="{S_MODE_ACTION}">
					<div class="title">查看</div>
					<div class="module">
						<div>
							跳转：
							{S_VIEW_SELECT}
						</div>
						<div>
							显示方式：
							{S_MODE_SELECT}
							{L_ORDER}
							{S_ORDER_SELECT}
						</div>
						<div><input type="submit" name="submit" value="查看" /></div>
					</div>
<!-- BEGIN switch_user_based -->
					<div class="title">{STATISTICS_FOR_USER}</div>
<!-- END switch_user_based -->
<!-- BEGIN view_all_attch -->
					<div class="title">所有用户的附件</div>
<!-- END view_all_attch -->
<!-- BEGIN not_attachrow -->
					<div class="module">没有上传任何附件</div>
<!-- END not_attachrow -->
<!-- BEGIN attachrow -->
					<div class="{attachrow.ROW_CLASS} module">
						<div>文件名：<a href="{attachrow.U_VIEW_ATTACHMENT}" target="_blank">{attachrow.FILENAME}</a></div>
						<div>描述：<input type="text" size="40" maxlength="200" name="attach_comment_list[]" value="{attachrow.COMMENT}" /></div>
						<div>扩展名：{attachrow.EXTENSION}</div>
						<div>大小：{attachrow.SIZE}</div>
						<div>下载次数：<input type="text" size="3" maxlength="10" name="attach_count_list[]" value="{attachrow.DOWNLOAD_COUNT}" /></div>
						<div>发表时间：{attachrow.POST_TIME}</div>
						<div>所属主题：{attachrow.POST_TITLE}</div>
						<div>{attachrow.S_DELETE_BOX} 删除</div>
						{attachrow.S_HIDDEN}
					</div>
<!-- END attachrow -->
					<div class="center">
						<br />
						<div>
							<input class="button" type="submit" name="submit_change" value="保存修改" />
							<input class="button" type="submit" name="delete" value="删除选中" />
						</div>
						<br />
						<div>
							<a class="button" href="#" onclick="marklist('attach_list', 'delete_id_list', true); return false;">选择全部</a>
							<a class="button" href="#" onclick="marklist('attach_list', 'delete_id_list', false); return false;">取消选择</a>
						</div>
						<br />
					</div>
<!-- BEGIN switch_user_based -->
					{S_USER_HIDDEN}
<!-- END switch_user_based -->
				</form>
				{PAGINATION}
			</div>
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