			<div id="main">
				<div class="title">签到</div>
				<p>小提示：每24小时可以签到一次，每次签到可以得到 <b>{ADD_POINTS}</b> {POINTS_NAME}，<b>{SIGN_USERNAME}</b> 记得每天来签到哦！</p>
<!-- BEGIN switch_no_sign -->
				<form action="{S_PROFILE_ACTION}" method="post">
					<textarea name="talk" rows="2" style="width:98%"></textarea><br />
					{S_HIDDEN_FORM_FIELDS}
					{SMILES_SELECT}
					<input type="submit" name="post" value="签到" />
				</form>
<!-- END switch_no_sign -->
<!-- BEGIN switch_yes_sign -->
				<div class="module"><b>您今天已经签到了，明天再来把</b></div>
<!-- END switch_yes_sign -->
				<div class="title">今日签到榜</div>
<!-- BEGIN ago_user -->
				<div class="module {ago_user.ROW_CLASS}">
					第 {ago_user.NUMBER} 名：<a href="{ago_user.U_UCP}">{ago_user.USERNAME}</a>
				</div>
<!-- END ago_user -->
				<div class="title">签到历史</div>
<!-- BEGIN sign_rows -->
				<div class="{sign_rows.ROW_CLASS}">
					{sign_rows.NUMBER}、<a href="{sign_rows.U_SIGN_VIEWPROFILE}">{sign_rows.SIGN_USERNAME}</a>签到说：{sign_rows.SIGN_TALK}[签到时间：{sign_rows.SIGN_TIME}]
				</div>
<!-- END sign_rows -->
				{PAGINATION}
				<div class="nav"><a href="{U_INDEX}">返回首页</a></div>
			</div>