			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;{MESSAGE_TITLE}</div>
				<div class="title">{MESSAGE_TITLE}</div>
				<form action="{S_CONFIRM_ACTION}" method="post">
					<p>{MESSAGE_TEXT}</p>
					{S_HIDDEN_FIELDS}
					<input type="submit" name="confirm" value="{L_YES}" />
					<input type="submit" name="cancel" value="{L_NO}" />
				</form>
			</div>