			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;帐号管理</div>
				<p>目前帐号激活的方式：{L_ACTIVATION}</p>
<!-- BEGIN switch_message -->
				<p><font color="green">{MESSAGE}</font></p>
<!-- END switch_message -->
				<form method="post" name="users" action="{S_ACCOUNT_ACTION}">
					<div class="title">显示方式</div>
						<select name="days">
							{S_SELECT_DAYS}
						</select>
						<input type="submit" name="submit_wait" value="显示" />
					</div>
					<div class="title">帐号列表</div>
<!-- BEGIN admin_account -->
					<div class="module {admin_account.ROW_CLASS}">
						<div>
							{admin_account.NUMBER}、<a href="{admin_account.U_PROFILE}">{admin_account.USERNAME}</a>
							<input type="checkbox" name="mark[]2" value="{admin_account.S_MARK_ID}" /> 
						</div>
						<div>电子邮件：{admin_account.EMAIL}</div>
						<div>注册时长：{admin_account.PERIOD}</div>
						<div>注册日期：{admin_account.JOINED}</div>
						<div><a href="{admin_account.U_EDIT_USER}">编辑资料</a> || <a href="{admin_account.U_USER_AUTH}">用户权限</a></div>
					</div>
<!-- END admin_account -->
<!-- BEGIN switch_no_users -->
					<div class="module">没有任何会员</div>
<!-- END switch_no_users -->
					<input type="submit" name="activate" value="{L_DE_ACTIVATE_MARKED}" />

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