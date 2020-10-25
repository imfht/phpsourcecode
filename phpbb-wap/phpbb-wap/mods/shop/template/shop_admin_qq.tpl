			<div id="main">
				<div class="title">出售ＱＱ号码管理</div>
<!-- BEGIN qq -->
				<div class="module {qq.ROW_CLASS}">
					{qq.QQ} 【<a href="{qq.U_DELETE}">删除</a>】<br />
					密码：{qq.PASSWORD}<br />
					需要{POINTS_NAME}：{qq.POINTS}
				</div>
<!-- END qq -->
				<div class="title">添加</div>
				<form action="{S_ACTION}" method="post">
					<div class="module">
						 ＱＱ号码：<br />
						<input type="text" name="qq" value="" maxlength="255" />
					</div>
					<div class="module">
						密码：<br />
						<input type="text" name="password" value="" maxlength="32" />
					</div>
					<div class="module">
						需要 <input type="text" name="points" value="1" size="5" maxlength="11" /> {POINTS_NAME}
					</div>
					<input type="submit" name="add" value="添加" />
				</form>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_ADMIN}">返回后台</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>