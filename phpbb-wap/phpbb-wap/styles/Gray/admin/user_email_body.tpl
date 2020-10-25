			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">管理面板</a>&gt;<a href="{U_ADMIN_INDEX}">管理首页</a>&gt;群发邮件</div>
				{ERROR_BOX}
				<p>邮件群发功能模块</p>
				<form method="post" action="{S_USER_ACTION}">
					<div class="title">邮件群发</div>
					<div class="row1">
						<div>选择发送对象：</div>
						<div>{S_GROUP_SELECT}</div>
					</div>
					<div class="row1">
						<div>要发送邮件的标题：</div>
						<div><input type="text" name="subject" value="{SUBJECT}" style="width: 235px;"/></div>
					</div>
					<div class="row1">
						<div class="row1">
							<div>您想要发送的邮件内容：</div>
							<div><textarea name="message" rows="5" cols="15" style="width: 235px;">{MESSAGE}</textarea></div>
						</div>
					</div>
					<div><input type="submit" value="发送邮件" name="submit" /></div>
				</form>
			</div>