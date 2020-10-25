			<div id="main">
				<div class="title">申请加入</div>
				{ERROR_BOX}
				<form action="{S_ACTION}" method="post">
					<div class="module">网站名称：<input type="text" name="title" value="" maxlength="8" /></div>
					<div class="module">网站类型：{SELECT_CAT}</div>
					<div class="module">网站简称：<input type="text" name="name" value="" maxlength="2" size="2" /></div>
					<div class="module">网站介绍：<textarea name="desc" maxlength="255"></textarea></div>
					<div class="module">网站地址：<input type="text" name="url" value="http://" maxlength="100" /></div>
					<div class="module"><input type="submit" name="submit" value="提交申请" /></div>
				</form>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>