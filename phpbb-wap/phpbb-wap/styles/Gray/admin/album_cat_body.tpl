			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;分类管理</div>
				<form action="{S_ALBUM_ACTION}" method="post">
					<div class="title">分类列表</div>
					<!-- BEGIN catrow -->
						<div class="module {catrow.ROW_CLASS}">
							<b>名称</b>: {catrow.TITLE}<br/>
							<b>描述</b>: {catrow.DESC}<br/>
							<a href="{catrow.S_MOVE_UP}">上移</a>, <a href="{catrow.S_MOVE_DOWN}">下移</a>, <a href="{catrow.S_EDIT_ACTION}">编辑</a>, <a href="{catrow.S_DELETE_ACTION}">删除</a>
						</div>
					<!-- END catrow -->
					<input type="hidden" value="new" name="mode" />
					<input name="submit" type="submit" value="创建分类" />
				</form>
			</div>