			<div id="main">
				<div class="title">论坛版块</div>
<!-- BEGIN catrow -->
				<div class="nav-title">
					{catrow.CAT_ICON}
					<a href="{catrow.U_VIEWCAT}" class="cattitle">{catrow.CAT_DESC}</a>
				</div>
	<!-- BEGIN forumrow -->
				<div class="forum-list">
					<div class="forum-icon">
						{catrow.forumrow.FORUM_ICON}
					</div>
					<dl>
						<dt>
							<a href="{catrow.forumrow.U_VIEWFORUM}">{catrow.forumrow.FORUM_NAME}</a>
						</dt>
						<dd>主题: {catrow.forumrow.TOPICS}<dd>
						<dd>帖子: {catrow.forumrow.POSTS}</dd>
					</dl>
				</div>
	<!-- END forumrow -->
				<div class="clear"></div>
<!-- END catrow -->
			</div>