			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;<a href="{U_PM_LIST}">信息列表</a>&gt;详细内容</div>
				<p>在这里，您可以查看所有存储在数据库中的信息，这有助于您清理论坛中乱发消息的用户</p>
				<div class="title">私人信息的详细内容</div>
				<form action="{S_DELETE_ACTION}" method="post">
					<div class="module">
						<div>标题：{SUBJECT}</div>
						<div>发信人：<a href="{FROM_URL}">{FROM}</a></div>
						<div>收信人：<a href="{TO_URL}">{TO}</a></div>
						<div>时间：{DATE}</div>
						<div>发送IP：{IP}</div>
						<div>类型：{TYPE}</div>
						<div>内容：{MESSAGE}</div>
					</div>
					<input type="hidden" name="pm_id_list[]" value="{ID}"/>
					<div class="center">
						<br />
							<input class="button" type="submit" value=" 删除 " />
						</div>
						<br />
					</div>
				</form>
			</div>