			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;管理员</div>
<!-- BEGIN switch_list_staff -->
	<!-- BEGIN user_level -->
				<div class="title">{switch_list_staff.user_level.USER_LEVEL}</div>
		<!-- BEGIN staff -->
				<div class="module">
					<a href="{switch_list_staff.user_level.staff.U_PROFILE}">{switch_list_staff.user_level.staff.USERNAME}</a>
					[{switch_list_staff.user_level.staff.POSTS}]
					{switch_list_staff.user_level.staff.USER_STATUS}
					{switch_list_staff.user_level.staff.FORUMS}
				</div>
		<!-- END staff -->
		<!-- BEGIN no_admin -->
				<div class="module">没有超级管理员</div>
		<!-- END no_admin -->
		<!-- BEGIN no_mod -->
				<div class="module">没有论坛版主</div>
		<!-- END no_mod -->

	<!-- END user_level -->
<!-- END switch_list_staff -->
			</div>