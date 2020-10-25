			<div id="mian">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;<a href="{U_ADMIN_FORUMS}">论坛列表</a>&gt;{L_TITLE}</div>
				<form action="{S_FORUM_ACTION}" method="post">
					<div class="title">{L_TITLE}</div>
					<div class="module">
						<label>名称：</label>
						<div><input type="text" name="cat_title" value="{CAT_TITLE}" /></div>
					</div>
					<div class="module">
						<label>ICON：</label>
						<div><input type="text" size="25" name="cat_icon" value="{CAT_ICON}" class="post" /></div>
					</div>
					{S_HIDDEN_FIELDS}
					<div><input type="submit" name="submit" value="{S_SUBMIT_VALUE}" /></div>
				</form>
			</div>