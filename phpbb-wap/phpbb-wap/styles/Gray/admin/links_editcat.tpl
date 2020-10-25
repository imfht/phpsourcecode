			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;<a href="{U_ADMIN_LINKS}">友链</a>&gt;编辑友链分类</div>
				<div class="title">编辑友链分类</div>
				<form action="{S_ACTION}" method="post">
					<div class="module">
						分类名：<br />
						<input type="text" name="name" value="{LINKCLASS_NAME}" />
					</div>
					<div class="module">
						排序（请输入数字）：<br />
						<input type="text" name="sort" value="{LINKCLASS_SORT}" size="2" />
					</div>
					<div class="module">
						描述：<br />
						<textarea name="desc">{LINKCLASS_DESC}</textarea>
					</div>
					<!-- BEGIN switch_delete -->
					<div class="module">
						<input type="checkbox" name="delete" /> 删除该分类
					</div>

					<!-- END switch_delete -->
					<input type="submit" name="submit" value="保存" />
				</form>
			</div>