			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;小组</div>
<!-- BEGIN switch_groups_joined -->
				<div class="title">小组列表</div>
				
	<!-- BEGIN switch_groups_member -->
				<div>
					{GROUP_MEMBER_SELECT}
				</div>
	<!-- END switch_groups_member -->
				
	<!-- BEGIN switch_groups_pending -->
				<div>
					等待审核的成员：<br />
					{GROUP_PENDING_SELECT}
				</div>
	<!-- END switch_groups_pending -->
<!-- END switch_groups_joined -->

<!-- BEGIN switch_groups_remaining -->
				<div class="title">申请加入小组</div>
				<div>
					请选择您喜欢的小组：<br />
					{GROUP_LIST_SELECT}
				</div>
<!-- END switch_groups_remaining -->
			</div>