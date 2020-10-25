<script language="javascript" type="text/javascript">
<!--
function update_smiley(newimage)
{
	document.smiley_image.src = "{S_SMILEY_BASEDIR}/" + newimage;
}
//-->
</script>
			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_SMILEY_ADMIN}">表情列表</a>&gt;配置</div>
				<form method="post" action="{S_SMILEY_ACTION}">
					<div class="title">配置</div>
					<p>如果新增一个表情请先把图标上传到 {SMILEY_PATH} 文件夹下</p>
					<div class="module bm-gray">
						<label>代码：</label>
						<div><input type="text" name="smile_code" value="{SMILEY_CODE}" /></div>
					</div>
					<div class="module bm-gray">
						<label>图标：</label>
						<div>
							<select name="smile_url" onchange="update_smiley(this.options[selectedIndex].value);">
								{S_FILENAME_OPTIONS}
							</select>
							<img name="smiley_image" src="{SMILEY_IMG}" border="0" alt="" />
						</div>
					</div>
					<div class="module">
						<label>描述：</label>
						<div>
							<input type="text" name="smile_emotion" value="{SMILEY_EMOTICON}" />
						</div>
					</div>
					{S_HIDDEN_FIELDS}
					<div class="left">
						<input type="submit" value="保存" />
					</div>
				</form>
			</div>