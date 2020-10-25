			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;在线状态</div>
				{HOBOE}
				<div class="title">在线状态统计</div>
				<div class="module">在线会员：{TOTAL_ONLINE_USER} 人</div>
				<div class="title">正在活跃的用户</div>
<!-- BEGIN online_row -->
				<div class="module {online_row.ROW_CLASS}">
					<div class="left">
						{online_row.USER_AVATER}
					</div>
					<div class="left">
						<div>
							<a href="{online_row.U_USER_PROFILE}">{online_row.USERNAME}</a>（<a href="http://www.ip138.com/ips138.asp?ip={online_row.PREV_IP}">{online_row.PREV_IP}</a>）
						</div>
						<p>在 {online_row.LASTUPDATE} 前访问了 <a href="{online_row.U_FORUM_LOCATION}">{online_row.FORUM_LOCATION}</a></p>
					</div>
					<div class="clear"></div>
				</div>
<!-- END online_row -->
<!-- BEGIN not_online_user -->
				<div class="module">除您之外没有人在线</div>
<!-- END not_online_user -->
				{PAGINATION}
			</div>