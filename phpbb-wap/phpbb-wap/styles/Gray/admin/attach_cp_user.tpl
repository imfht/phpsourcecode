			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;用户附件</div>
				<form method="post" action="{S_MODE_ACTION}">
					<div class="title">查看</div>
					<div class="module">
						<div>
							跳转：
							{S_VIEW_SELECT}
						</div>
						<div>
							显示方式：
							{S_MODE_SELECT}
							{L_ORDER}
							{S_ORDER_SELECT}
						</div>
						<div><input type="submit" name="submit" value="查看" /></div>
					</div>
				</form>
				<div class="title">附件用户列表</div>
<!-- BEGIN empty_memberrow -->
				<div class="module">没有任何用户上传有附件</div>
<!-- END empty_memberrow -->

<!-- BEGIN memberrow -->
				<div class="{memberrow.ROW_CLASS} module">
					<div>用户名：<a href="{memberrow.U_VIEW_MEMBER}">{memberrow.USERNAME}</a></div>
					<div>总附件：{memberrow.TOTAL_ATTACHMENTS}</div>
					<div>总附件大小：{memberrow.TOTAL_SIZE}</div>
				</div>
<!-- END memberrow -->
				{PAGINATION}
			</div>