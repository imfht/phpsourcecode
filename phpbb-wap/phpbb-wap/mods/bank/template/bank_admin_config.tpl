			<div id="main">
				<div class="title">银行信息</div>
				<div class="module row2">
					总资产：{BANK_HOLDING}
				</div>
				<div class="module row1">
					取款统计：{BANK_WITHDRAWS}
				</div>
				<div class="module row2">
					存款统计：{BANK_DEPOSITS}
				</div>
				<div class="module row1">
					账户：{BANK_ACCOUNTS}
				</div>
				<div class="title">编辑账户</div>
				<form method="post" action="{S_CONFIG_ACTION}" name="post">
					<div class="module">
						<p>您要编辑的用户账户，请输入用户名</p>
						<input type="text" class="post" name="username" maxlength="25">
						<input type="hidden" name="action" value="edit_account" />
						<input type="submit" value="确定" />
					</div>
				</form>
				<div class="title">虚拟银行参数设定</div>
				<form action="{S_CONFIG_ACTION}" method="post">
					<div class="module row2">
						虚拟银行状态：
						<select name="status">
							<option value="on" {SELECT_STATUS_ON}>开启</option>
							<option value="off" {SELECT_STATUS_OFF}>关闭</option>
						</select>
					</div>
					<div class="module row1">
						虚拟银行名称：
						<input type="text" name="name" value="{BANK_NAME}" maxlength="32" />
					</div>
					<div class="module row2">
						虚拟银行利率：
						<input type="text" name="interestrate" size="3" value="{BANK_INTEREST}" maxlength="3" />%
					</div>
					<div class="module row1">
						虚拟银行关闭利率（百分之）：
						<input type="text" name="disableinterest" maxlength="14" value="{BANK_DISABLE_INTEREST}" size="3" /> {L_POINTS} 0表示禁用
					</div>
					<div class="module row2">
						如果存款未到期限取出收取服务费：
						<input type="text" name="withdrawfee" size="3" value="{BANK_FEES}" maxlength="3" />%
					</div>
					<div class="module row1">
						存款最低限制：
						<input type="text" name="min_depo" size="3" value="{BANK_MIN_DEPO}" maxlength="10" /> {L_POINTS}
					</div>
					<div class="module row2">
						取款最低限制：
						<input type="text" name="min_with" size="3" value="{BANK_MIN_WITH}" maxlength="10" /> {L_POINTS}
					</div>
					<div class="module row1">
						利率支付周期（单位/秒）：
						<input type="text" class="post" name="paymenttime" value="{BANK_PAY_TIME}" maxlength="14" /> 
					</div>
					<input type="hidden" name="action" value="update_config" />
					<input type="submit" class="liteoption" value="更新" />
				</form>
				<div class="nav"><a href="{U_ADMIN_MODS}">返回上级</a> / <a href="{U_ADMIN}">返回后台</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>