			<div id="mian">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;<a href="{U_ADMIN_FORUMS}">论坛列表</a>&gt;删除论坛</div>
				<form action="{S_FORUM_ACTION}" method="post">
					<div class="title">删除论坛</div>
					<div class="module">
						<label>论坛：</label>
						{NAME}
					</div>
					<div class="module">
						<label>移动主题到：</label>
						{S_SELECT_TO}
					</div>
					{S_HIDDEN_FIELDS}
					<input type="submit" name="submit" value="{S_SUBMIT_VALUE}" />
				</form>
			</div>