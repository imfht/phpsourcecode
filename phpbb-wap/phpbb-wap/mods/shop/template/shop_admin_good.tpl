			<div id="main">
				<div class="title">精彩内容管理</div>
<!-- BEGIN good -->
				<div class="module {good.ROW_CLASS}">
					{good.GOOD} 【<a href="{good.U_DELETE}">删除</a>】
					<p>URL：{good.GOOD_URL}</p>
					<p>用户每次点击可获得 <strong>{good.GOOD_POINTS}</strong> {POINTS_NAME}</p>
				</div>
<!-- END good -->
				<div class="title">添加</div>
				<form action="{S_ACTION}" method="post">
					<div class="module">
						标题或名称：<br />
						<input type="text" name="name" value="" maxlength="255" />
					</div>
					<div class="module">
						URL：<br />
						<input type="text" name="url" value="http://" maxlength="255" />
					</div>
					<div class="module">
						用户点击一次可以获得 <input type="text" name="points" value="1" size="5" maxlength="11" /> {POINTS_NAME}
					</div>
					<input type="submit" name="add" value="添加" />
				</form>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_ADMIN}">返回后台</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>