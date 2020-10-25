			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;会员列表</div>
				<p>本站共有会员 {TOTAL_USERS} 人！</p>
				<form action="{U_LIST_ACTION}" method="post">
					<div class="title">显示方式</div>
					<div>
						<select name="sort">
							<option value="user_id" class="genmed" {ID_SELECTED} >ID</option>
							<option value="username" class="genmed" {USERNAME_SELECTED} >用户名</option>
							<option value="user_posts" class="genmed" {POSTS_SELECTED} >帖子</option>
							<option value="user_lastvisit" class="genmed" {LASTVISIT_SELECTED} >上次登录</option>
						</select>
						<select name="order">
							<option value="" {ASC_SELECTED} >从低到高</option>
							<option value="DESC" {DESC_SELECTED} >从高到低</option>
						</select>
						<input type="submit" value="显示">
					</div>
				</form>
				<div class="title">会员列表</div>
				<p>提示：要修改用户的资料请点击Ta的用户名！</p>
<!-- BEGIN userrow -->
				<div class="{userrow.ROW_CLASS} module">
					<div>
						{userrow.NUMBER}、【<strong><a href="{userrow.U_ADMIN_USER}">{userrow.USERNAME}</a></strong>】
						<a href="{userrow.U_ADMIN_USER_AUTH}">权限</a>
					</div>
					<div>用户ID：{userrow.NUMBER}</div>
					<div>发表帖子：{userrow.POSTS}</div>
					<div>电子邮箱：{userrow.EMAIL}</div>
					<div>注册日期：{userrow.JOINED}</div>
					<div>最后访问：{userrow.LAST_VISIT}</div>
					<div>是否激活：{userrow.ACTIVE}</div>
				</div>
<!-- END userrow -->
				{PAGINATION}
			</div>