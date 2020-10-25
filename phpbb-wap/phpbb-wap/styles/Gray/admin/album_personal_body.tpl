			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;个人相册管理</div>
				<form action="{S_ALBUM_ACTION}" method="post">
					<div class="title">列表</div>
						<!-- BEGIN grouprow -->
							<div class="module {grouprow.ROW_CLASS}">
								<b>{grouprow.GROUP_NAME}</b>：<br />
								<input name="private[]" type="checkbox" {grouprow.PRIVATE_CHECKED} value="{grouprow.GROUP_ID}" /> 可以查看用户相册
							</div>
						<!-- END grouprow -->
					<input name="submit" type="submit" value="保存" />
				</form>
			</div>