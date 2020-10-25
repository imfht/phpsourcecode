			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;附件统计</div>
				<p>附件的统计可能会消耗大量的服务器资源</p>
				<div class="title">您可以请选择</div>
				<form method="post" action="{S_MODE_ACTION}">
					<div class="module">
						{S_VIEW_SELECT} <input type="submit" name="submit" value="执行" />
					</div>
				</form>
				<div class="title">统计结果</div>
				<div class="module bm-gray">附件的总数：<strong>{NUMBER_OF_ATTACHMENTS}</strong></div>
				<div class="module bm-gray">存放附件的目录大小：<strong>{TOTAL_FILESIZE}</strong></div>
				<div class="module bm-gray">附件限制：<strong>{ATTACH_QUOTA}</strong></div>
				<div class="module bm-gray">帖子中的附件数量：<strong>{NUMBER_OF_POSTS}</strong></div>
				<div class="module bm-gray">主题中的附件数量：<strong>{NUMBER_OF_TOPICS}</strong></div>
				<div class="module bm-gray">特殊用户发表的附件数量：<strong>{NUMBER_OF_USERS}</strong></div>
			</div>