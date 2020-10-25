			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;发送邮件</div>
				{ERROR_BOX}
				<form action="{S_POST_ACTION}" method="post" name="post">
					<div class="title">给 {USERNAME} 发送邮件</div>
					<div class="module">
						<label>标题：</label>
						<div><input type="text" name="subject" maxlength="100" value="{SUBJECT}" /></div>
					</div>
					<div class="module">
						<label>内容：</label>
						<div><textarea name="message" rows="5" style="width:99%;">{MESSAGE}</textarea></div>
					</div>
					{S_HIDDEN_FIELDS}
					<input type="submit" name="submit" value="发送" />
				</form>
			</div>