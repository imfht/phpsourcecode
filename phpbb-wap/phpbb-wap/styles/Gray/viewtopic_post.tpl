				<form action="{S_POST_ACTION}" method="post">
					<div class="title">文明上网，理性回帖</div>
<!-- BEGIN switch_username_select -->
					<div class="module">用户名: <input type="text" name="username" /></div>
<!-- END switch_username_select -->
					<div class="module">
<!-- BEGIN bb_panel -->
						<div id="bbcode" style="font-weight: bold;">
							<a href="javascript:void(0);" onclick="bbcode('b');">加粗</a>
							<a href="javascript:void(0);" onclick="bbcode('url');">链接</a>
							<a href="javascript:void(0);" onclick="bbcode('code');">代码</a>
							<a href="javascript:void(0);" onclick="bbcode('i');">倾斜</a>
							<a href="javascript:void(0);" onclick="bbcode('u');">底线</a>
							<a href="javascript:void(0);" onclick="bbcode('color');">颜色</a><br />
							<a href="javascript:void(0);" onclick="bbcode('size');">大小</a>
							<a href="javascript:void(0);" onclick="bbcode('img');">贴图</a>
							<a href="javascript:void(0);" onclick="bbcode('quote');">引用文字</a>
							<a href="javascript:void(0);" onclick="bbcode('quote_username');">引用用户</a>
						</div>
<!-- END bb_panel -->
						<textarea id="text" name="message" rows="4" style="width:99%" onkeydown="if(event.ctrlKey&&event.keyCode==13){document.getElementById('misubmit').click();return false};"></textarea>
					</div>
					{S_HIDDEN_FORM_FIELDS}
					<div class="module">
						{SMILES_SELECT}
						<input id="misubmit" class="subbutton" type="submit" name="post" value="快速回复" /> . <a href="{U_POST_REPLY_TOPIC}" class="buttom">高级回复</a>
					</div>
				</form>