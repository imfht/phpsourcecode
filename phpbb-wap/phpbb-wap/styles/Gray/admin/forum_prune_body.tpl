			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_SELECT_FORUM}">选择论坛</a>&gt;{FORUM_NAME}</div>
				<p>这将删除所有在限定时间内没有回复的主题，如果您没有指定时限（日数），所有的主题都将会被删除，但是无法删除正在进行中的投票主题或是公告，您必须手动移除这些主题</p>
				<form method="post" action="{S_FORUMPRUNE_ACTION}">
					<div class="title">{FORUM_NAME}</div>
					<div class="module">
						清理
						{S_PRUNE_DATA}
						天没有回复的主题
					</div>
						{S_HIDDEN_VARS}
					<div class="center">
						<input type="submit" name="doprune" value="执行清理" />
					</div>
				</form>
			</div>