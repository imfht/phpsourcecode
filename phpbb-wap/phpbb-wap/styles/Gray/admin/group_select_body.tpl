			<div class="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;小组</div>
				<p>在这个控制面板里您可以管理所有的用户小组，您可以建立、删除以及编辑现存的用户小组，您可以指定小组管理员，设定小组模式（开放、封闭、隐藏）以及小组的名称和描述</p>
				<div class="title">选择小组</div>
				<form method="post" action="{S_GROUP_ACTION}">
<!-- BEGIN select_box -->
					<div class="module">
						{S_GROUP_SELECT}
						<input type="submit" name="edit" value="查看" />
					</div>
<!-- END select_box -->
					{S_HIDDEN_FIELDS}
					<input type="submit" name="new" value="创建小组" />
				</form>
			</div>