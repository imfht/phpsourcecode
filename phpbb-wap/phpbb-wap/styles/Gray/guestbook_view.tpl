			<div id="main">
				<div class="title">{GB_TITLE}</div>
				<div class="module bm-gray">时间：{GB_TIME}</div>
				<div class="module bm-gray">留言人：{GB_USERNAME} ({GB_IP})</div>
				<div class="module bm-gray">内容：{GB_TEXT}</div>
				<div class="module">回复：{GB_REPLY}</div>
<!-- BEGIN delete -->
				<div class="module"><a href="{U_GB_DELETE}">删除</a> (注意：删除没有确认操作)</div>
<!-- END delete -->
<!-- BEGIN reply -->
				<div class="title">回复该留言</div>
				<form action="{S_ACTION}" method="post">
					<div class="module"><textarea name="reply" rows="5"></textarea></div>
					<input type="submit" value="提交回复" />
				</form>
<!-- END reply -->
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>