			<div id="main">
				<div class="title">虚拟银行</div>
				<!-- BEGIN has_account -->
					<div class="row1">{L_ACTIONS}</div>
					<form method="post" action="{U_DEPOSIT}">
						<div class="module row1">
							<p>小提示：您的网站账户有 {USER_GOLD} {L_POINTS}</p>
							<p>
								我想存入 <input type="text" class="post" name="deposit" value="" size="3"/> {L_POINTS}
								<input type="submit" name="Deposit" value="确认" />
							</p>
						</div>
					</form>
					<form method="post" action="{U_WITHDRAW}">
						<div class="module row2">
							<p>小提示：您的虚拟银行账户有 {USER_WITHDRAW} {L_POINTS}</p>
							<p>
								我想取款 <input type="text" class="post" name="withdraw" value="" size="3" /> {L_POINTS}
								<input type="submit" name="Withdraw" value="确认">
							</p>
						</div>
					</form>
				<!-- END has_account -->
				<!-- BEGIN no_account -->
					<p>您还没有虚拟银行账户，一秒创建，方便快捷！</p>
					<div class="module">点击 <a href="{no_account.U_OPEN_ACCOUNT}">这里</a> 办理开户手续</div>
				<!-- END no_account -->
					<div class="title">银行信息</div>
				<!-- BEGIN has_account -->
					<div class="module">我的虚拟银行账户：{USER_BALANCE} {L_POINTS}</div>
				<!-- END has_account -->
					<div class="module">利率：{BANK_INTEREST}%</div>
				<!-- BEGIN switch_withdraw_fees -->
					<div class="module">取款服务费：{BANK_FEES} %</div>
				<!-- END switch_withdraw_fees -->
				<!-- BEGIN switch_min_depo -->
					<div class="module">存款最低限制：{BANK_MIN_DEPO} {L_POINTS}</div>
				<!-- END switch_min_depo -->
				<!-- BEGIN switch_min_with -->
					<div class="module">取款最低限制：{BANK_MIN_WITH} {L_POINTS}</div>
				<!-- END switch_min_with -->
					<div class="module">拥有账户：{BANK_ACCOUNTS}</div>
					<div class="module">银行资产：{BANK_HOLDINGS} {L_POINTS}</div>
					<div class="module">银行状态：{BANK_OPENED}</div>
				</div> 
				<div class="nav"><a href="{U_INDEX}">返回首页</a></div>
			</div>