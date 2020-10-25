			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_USER_PROFILE}">用户中心</a>&gt;转账</div>
				{ERROR_BOX}
				<form action="{S_POST_ACTION}" method="post">
					<div class="title">转给{USERNAME}</div>
					<p>请输入{POINT_NAME}数目(您有 {USER_MONEY}{POINT_NAME})</p>
					<input type="text" name="money_send"/>
					<input type="submit" name="submit" value="确认" />
				</form>
			</div>