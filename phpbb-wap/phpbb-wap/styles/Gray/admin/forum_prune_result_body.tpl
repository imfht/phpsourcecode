			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_SELECT_FORUM}">选择论坛</a>&gt;清理结果</div>
				<div class="title">清理结果</div>
<!-- BEGIN prune_results -->
				<div class="{prune_results.ROW_CLASS} module">
					<div>
						<label>论坛：</label>
						{prune_results.FORUM_NAME}
					</div>
					<div>
						<label>主题：</label>
						{prune_results.FORUM_TOPICS}
					</div>
					<div>
						<label>帖子：</label>
						{prune_results.FORUM_POSTS}
					</div>
				</div>
<!-- END prune_results -->
			</div>