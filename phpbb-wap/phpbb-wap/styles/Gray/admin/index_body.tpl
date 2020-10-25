			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级后台</a>&gt;论坛统计</div>
				<div class="title">论坛统计</div>
				<div class="module bm-gray">
					论坛的总主题数量：<b>{NUMBER_OF_POSTS}</b>
				</div>
				<div class="module bm-gray">
					论坛的总帖子数量: <b>{NUMBER_OF_TOPICS}</b>
				</div>
				<div class="module bm-gray">
					平均每天发表主题数量: <b>{TOPICS_PER_DAY}</b>
				</div>
				<div class="module bm-gray">
					平均每天发表帖子数量：<b>{POSTS_PER_DAY}</b>
				</div>
				<div class="module bm-gray">
					论坛已注册用户数量：<b>{NUMBER_OF_USERS}</b>
				</div>
				<div class="module bm-gray">
					平均每日注册用户数量：<b>{USERS_PER_DAY}</b>
				</div>
				<div class="module bm-gray">
					网站建设日期：<b>{START_DATE}</b>
				</div>
				<div class="module bm-gray">
					头像目录大小：<b>{AVATAR_DIR_SIZE}</b>
				</div>
				<div class="module bm-gray">
					数据库大小：<b>{DB_SIZE}</b>
				</div>
				<div class="title">在线状态</div>
<!-- BEGIN reg_user_row -->
				<div class="{reg_user_row.ROW_CLASS}">
				用户 <a href="{reg_user_row.U_USER_PROFILE}">{reg_user_row.USERNAME}</a>（访问IP：<a href="{reg_user_row.U_WHOIS_IP}">{reg_user_row.IP_ADDRESS}</a>）正在访问 <strong>{reg_user_row.FORUM_LOCATION}</strong>
				</div>
<!-- END reg_user_row -->
<!-- BEGIN guest_user_row -->
				<div class="{guest_user_row.ROW_CLASS}">
				{guest_user_row.USERNAME}（访问IP：<a href="{guest_user_row.U_WHOIS_IP}">{guest_user_row.IP_ADDRESS}</a>）正在访问 <strong>{guest_user_row.FORUM_LOCATION}</strong>
				</div>
<!-- END guest_user_row -->
			</div>