			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_FORUM}">论坛中心</a>&gt;<a href="{U_VIEWFORUM}">{FORUM_NAME}</a>&gt;<a href="{U_MODCP}">版主管理面板</a>&gt;IP信息</div>
				<div class="title">发表本贴使用的 IP 是</div>
				<div class="module">
					<a href="{U_LOOKUP_IP}">{IP}</a>[帖子：{POSTS}]
				</div>
				<div class="title">使用该 IP 的用户有</div>
<!-- BEGIN userrow -->
				<div class="module {userrow.ROW_CLASS}">
					<a href="{userrow.U_PROFILE}">{userrow.USERNAME}</a>
					[{userrow.POSTS}]
					<a href="{userrow.U_SEARCHPOSTS}">Ta发表的主题</a>
				</div>
<!-- END userrow -->
				<div class="title">该用户曾经使用的其他 IP 有</div>
<!-- BEGIN iprow -->
				<div class="module {iprow.ROW_CLASS}">
					{iprow.IP}
					[{iprow.POSTS}]
					<a href="{iprow.U_LOOKUP_IP}">搜索IP</a>
				</div>
<!-- END iprow -->
<!-- BEGIN not_iprow -->
				<div class="module">该用户没有使用过其它IP</div>
<!-- END not_iprow -->