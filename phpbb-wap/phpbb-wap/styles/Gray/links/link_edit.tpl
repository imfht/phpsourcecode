			<div id="main">
				<div class="title">修改您的网站资料</div>
				<form action="{S_ACTION}" method="post">
					<div class="module">网站名称：<input type="text" name="title" value="{LINK_TITLE}" maxlength="8" /></div>
					<div class="module">网站简称：<input type="text" name="name" value="{LINK_NAME}" maxlength="2" size="2" /></div>
					<div class="module">网站介绍：<textarea name="desc" maxlength="255">{LINK_DESC}</textarea></div>
					<div class="module">网站地址：<input type="text" name="url" value="{LINK_URL}" maxlength="100" /></div>
					<div class="module">创建时间：{LINK_JOIN}</div>
					<div class="module">上次链入：{LAST_VISIT}</div>
					<div class="module">入站：{LINK_IN}</div>
					<div class="module">出站：{LINK_OUT}</div>
					<div class="module">审核状况：{LINK_SHOW}</div>
					<div class="module"><input type="submit" name="submit" value="保存" /></div>
				</form>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>