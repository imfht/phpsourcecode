			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_ALBUM_LISTS}">分类列表</a>&gt;删除</div>
				<form action="{S_ALBUM_ACTION}" method="post">
					<div class="title">删除</div>
					<div class="module">
						选中的分类 <strong>{S_CAT_TITLE}</strong>
					</div>
					<div class="module">
						转移 {S_SELECT_TO}
					</div>
					<input type="hidden" name="mode" value="delete" />
					<input type="submit" name="submit" value="删除" />
				</form>