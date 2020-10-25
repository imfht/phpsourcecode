			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_FORUM}">论坛中心</a>&gt;<a href="{U_VIEW_FORUM}">{FORUM_NAME}</a>&gt;<a href="{U_MODCP}">版主面板</a>&gt;分割帖子</div>
				<form method="post" action="{S_SPLIT_ACTION}">
					<div class="title">主题的帖子列表</div>
<!-- BEGIN postrow -->
					<div class="{postrow.ROW_CLASS}">
						<div class="nav">
							{postrow.S_SPLIT_CHECKBOX}
							作者：{postrow.POSTER_NAME}
							[{postrow.POSTER_POSTS}]
							发表时间：{postrow.POST_DATE}
						</div>
						<p>内容：{postrow.MESSAGE}</p>
					</div>
<!-- END postrow -->
					<div class="title">选项</div>
					<div class="module">
						标题：<input type="text" maxlength="60" name="subject" />
					</div>
					<div class="module">
						发表论坛：
						{S_FORUM_SELECT}
					</div>
					<input type="submit" name="split_type_all" value="分割已选贴子" />
					<input type="submit" name="split_type_beyond" value="选择分割贴子" />
					{S_HIDDEN_FIELDS}
				</form>
			</div>