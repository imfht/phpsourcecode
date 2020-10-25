			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;等级</div>
				<div class="title">管理用户等级</div>
<!-- BEGIN ranks -->
				<div class="{ranks.ROW_CLASS} module">
					<div>{ranks.L_NUMBER}、{ranks.RANK}</div>
					<div>当用户发表 {ranks.RANK_MIN} 帖子后获得</div>
					<div>特殊等级：{ranks.SPECIAL_RANK}</div>
					<div><a href="{ranks.U_RANK_EDIT}">编辑</a> | <a href="{ranks.U_RANK_DELETE}">删除</a></div>
				</div>
<!-- END ranks -->
				<form method="post" action="{S_RANKS_ACTION}">
					<input type="submit" name="add" value="新建等级" />
				</form>
			</div>