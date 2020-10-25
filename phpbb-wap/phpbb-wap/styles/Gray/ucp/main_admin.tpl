			<div id="main">
				<div class="title">修改空间主页</div>
				<form action="{S_ACTION}" method="post">
					<div class="module row1">
						空间标题：<br />
						<input type="text" name="name" value="{S_MAIN_TITLE}">
					</div>
					<div class="module row2">
						head部分：<br />
						<textarea name="head" rows="8" style="width:99%;">{S_MAIN_HEAD}</textarea>
					</div>
					<div class="module row1">
						body部分：<br />
						<textarea name="body" rows="8" style="width:99%;">{S_MAIN_BODY}</textarea>
					</div>
					<input type="submit" name="submit" value="保存" />
				</form>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>