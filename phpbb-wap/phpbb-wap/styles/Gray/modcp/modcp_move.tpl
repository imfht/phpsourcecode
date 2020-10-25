			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;{MESSAGE_TITLE}</div>
				<form action="{S_MODCP_ACTION}" method="post">
					<div class="title">{MESSAGE_TITLE}</div>
					<div class="module">
						移动到
						{S_FORUM_SELECT}
					<div>
					<div>
						<input type="checkbox" name="move_leave_shadow" /> 保留原有主题
					</div>
					<p>{MESSAGE_TEXT}</p>
					{S_HIDDEN_FIELDS}
					<input class="subbutton" type="submit" name="confirm" value="{L_YES}" />
					<input class="subbutton" type="submit" name="cancel" value="{L_NO}" />
				</form>
			</div>