			<div id="main">
				<div class="title">{L_TITLE}</div>
				{ERROR_BOX}
				<form action="{S_ACTION}" method="post">
					<div class="module row1">
						标题：<br />
						{ARTICLE_CLASS}
<!-- BEGIN admin -->
						(<a href="{admin.CLASS_CREATE}">添加</a>/<a href="{admin.CLASS_MANAGE}">管理</a>)
<!-- END admin -->
						<input type="text" name="title" value="{ARTICLE_TITLE}" maxlength="64" />
					</div>
					<div class="module row2">
						内容：
							<a href="javascript:void(0);" onclick="bbcode('url');">链接</a>
							<a href="javascript:void(0);" onclick="bbcode('img');">贴图</a>
						<br />
						<textarea id="article_text" name="text" rows="5" style="width:100%;">{ARTICLE_TEXT}</textarea>
					</div>
					<input type="submit" name="submit" value="保存" />
				</form>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>
			<script type="text/javascript">
			function bbcode(Tag) {
				var Open='['+Tag+']';
				var Close='[/'+Tag+']';
				var object = document.getElementById('article_text');
				object.focus();
				if (Open=='[url]'){
					var Open='['+Tag+'=http://]';
				}
				if (window.attachEvent && navigator.userAgent.indexOf('Opera') === -1) {
					var s = object.sel;
					if(s){
						var l = s.text.length;
						s.text = Open + s.text + Close;
						s.moveEnd("character", -Close.length);
						s.moveStart("character", -l);
						s.select();
					}
				} else {
					var ss = object.scrollTop;
					sel1 = object.value.substr(0, object.selectionStart);
					sel2 = object.value.substr(object.selectionEnd);
					sel = object.value.substr(object.selectionStart, object.selectionEnd - object.selectionStart);
					object.value = sel1 + Open + sel + Close + sel2;
					object.selectionStart = sel1.length + Open.length;
					object.selectionEnd = object.selectionStart + sel.length;
					object.scrollTop = ss;
				}
			}
			</script>