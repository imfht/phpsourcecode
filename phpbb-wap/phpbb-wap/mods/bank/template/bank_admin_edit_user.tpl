			<div id="main">
				<form action="{S_CONFIG_ACTION}" method="post">
					<div class="title">编辑虚拟银行账户</div>
					<div class="module row1">
						账户金额：
						<input type="text" class="post" name="holding"  maxlength="10" value="{USER_HOLDING}" size="5" />
					</div>
					<div class="module row1">
						存款次数：
						<input type="text" name="withdrawn" size="5" maxlength="10" value="{USER_WITHDRAWN}" />
					</div>
					<div class="module row2">
						取款次数：
						<input type="text" name="deposited" size="5" maxlength="10" value="{USER_DEPOSITED}" />
					</div>
					<div class="row1">
						取款费用：
						<select name="fees">
							<option value="on" {SELECT_FEES_ON}>需要</option>
							<option value="off" {SELECT_FEES_OFF}>无需</option>
						</select>
					</div>
					<input type="hidden" name="action" value="update_account" />
					<input type="hidden" name="user_id" value="{USER_ID}" />
					<input type="submit" value="更新" />
				</form>
				<div class="nav"><a href="{U_ADMIN_BANK}">返回上级</a> / <a href="{U_ADMIN}">返回后台</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>