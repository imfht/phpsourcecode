			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;删除会员</div>
				<p>在这里，您可以批量删除会员，本站共有注册会员 {TOTAL_USERS} 人，删除用户是没有确认提示的，且删除后不可恢复！</p>
				<div class="title">会员列表</div>
				<div class="module bm-gray">
					<form  action="{U_LIST_ACTION}" method="post">		
						<select name="sort">
							<option value="user_id" {ID_SELECTED} >用户ID</option>
							<option value="username" {USERNAME_SELECTED} >用户名</option>
							<option value="user_posts" {POSTS_SELECTED} >帖子数量</option>
							<option value="user_lastvisit" {LASTVISIT_SELECTED} >最后访问</option>
						</select>
						<select name="order">
							<option value="" {ASC_SELECTED} >从低到高</option>
							<option value="DESC" {DESC_SELECTED} >从高到低</option>
						</select>
						<input type="submit" value="显示">
					</form>
				</div>
				<form id="user_list" action="{U_DELETE_ACTION}" method="post">
<!-- BEGIN userrow -->
					<div class="{userrow.ROW_CLASS} module">
						<input type="checkbox" name="user_id_list[]" value="{userrow.NUMBER}" />
						{userrow.L_NUMBER}、
						<a href="{userrow.U_ADMIN_USER}">{userrow.USERNAME}</a><br />
						发帖数量：<b>{userrow.POSTS}</b><br />
						注册日期：<b>{userrow.JOINED}</b><br />
						最后访问：<b>{userrow.LAST_VISIT}</b>
					</div>
<!-- END userrow -->
					<br />
					<div class="center">
						<div><input class="button" type="submit" value="删除选中" /></div>
						<br />
						<div>
							<a class="button" href="#" onclick="marklist('user_list', 'user_id_list', true); return false;">选择全部</a>
							<a class="button" href="#" onclick="marklist('user_list', 'user_id_list', false); return false;">取消选择</a>
						</div>
					</div>
					<br />
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