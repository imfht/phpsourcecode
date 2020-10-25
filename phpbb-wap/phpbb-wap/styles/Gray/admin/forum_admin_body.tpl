			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;论坛</div>
				<form method="post" action="{S_FORUM_ACTION}">
<!-- BEGIN catrow -->
					<div class="nav-title"><a href="{catrow.U_VIEWCAT}">{catrow.CAT_TITLE}</a></div>
					<div class="nav-row">
						<a href="{catrow.U_CAT_EDIT}">编辑</a>,
						<a href="{catrow.U_CAT_DELETE}">删除</a>,
						<a href="{catrow.U_CAT_MOVE_UP}">上移</a>,
						<a href="{catrow.U_CAT_MOVE_DOWN}">下移</a>
					</div>
					
	<!-- BEGIN forumrow -->
					<div class="nav-row">
						<div>论坛：<a href="{catrow.forumrow.U_VIEWFORUM}">{catrow.forumrow.FORUM_NAME}</a></div>
						<div>描述：{catrow.forumrow.FORUM_DESC}</div>
						<div>主题：{catrow.forumrow.NUM_TOPICS}</div>
						<div>帖子：{catrow.forumrow.NUM_POSTS}</div>
						<div>
							<a href="{catrow.forumrow.U_FORUM_EDIT}">编辑</a>,
							<a href="{catrow.forumrow.U_FORUM_DELETE}">删除</a>,
							<a href="{catrow.forumrow.U_FORUM_MOVE_UP}">上移</a>,
							<a href="{catrow.forumrow.U_FORUM_MOVE_DOWN}">下移</a>,
							<a href="{catrow.forumrow.U_FORUM_RESYNC}">同步</a>
						</div>
					</div>
	<!-- END forumrow -->
					<div class="module">
						<input type="text" name="{catrow.S_ADD_FORUM_NAME}" />
						<input type="submit" name="{catrow.S_ADD_FORUM_SUBMIT}" value="创建论坛" />
					</div>
<!-- END catrow -->
					<div class="module">
						<input type="text" name="categoryname" />
						<input type="submit" name="addcategory" value="创建论坛分类" />
					</div>
				</form>
			</div>