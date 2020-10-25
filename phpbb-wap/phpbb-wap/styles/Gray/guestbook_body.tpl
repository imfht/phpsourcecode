			<div id="main">
				<div class="title">留言板</div>
<!-- BEGIN guestbook_row -->
				<div class="module {guestbook_row.ROW_CLASS}">
					{guestbook_row.GB_NUMBER}、[{guestbook_row.GB_REPLY}]
					<a href="{guestbook_row.U_GB}">{guestbook_row.GB_TITLE}</a>
					（{guestbook_row.GB_TIME}）
				</div>
<!-- END guestbook_row -->
<!-- BEGIN not_guestbook -->
				<div class="module">没有任何留言记录</div>
<!-- END not_guestbook -->
				{PAGINATION}
				<form action="{S_ACTION}" method="post">
					<div class="title">发布留言</div>
					{ERROR_BOX}
					<div class="module">
						您的姓名：
						<input type="text" name="username" value="" />
					</div>
					<div class="module">
						留言标题：
						<input type="text" name="title" value="" />			
					</div>
					<div class="module">
						查看密码：
						<input type="text" name="password" value="" />
						<p>设置了查看密码后除了管理员其他人是无法看到您的留言内容的，您可以使用此密码查看留言，如果不想设置密码请留空</p>
					</div>
					<div class="module">
						留言问题：
						<input type="text" name="code" value="" />
						<p>请在上面的输入框输入 {L_SERVER_NAME}</p>
					</div>
					<div class="module">
						内容：<br />
						<textarea name="message" style="width:50%;" rows="5"></textarea>
					</div>
					<div class="module"><input type="submit" value="提交留言"></div>
				</form>
				<div class="nav"><a href="{U_INDEX}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>