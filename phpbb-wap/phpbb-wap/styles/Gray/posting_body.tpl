<script type="text/javascript">
function bbcode(Tag) {
	var Open='['+Tag+']';
	var Close='[/'+Tag+']';
	var object = document.getElementById('text');
	object.focus();
	if (Open=='[url]'){
		var Open='['+Tag+'=http://]';
	}
	else if (Open=='[color]')
	{
		var Open='['+Tag+'=#00ff00]';
	}
	else if (Open=='[size]')
	{
		var Open='['+Tag+'=5]';
	}
	else if(Open=='[quote_username]')
	{
		var Open = '[quote="作者"]';
		var Close = '[/quote]';
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
			<div id="main">
				{ERROR_BOX}
				<form action="{S_POST_ACTION}" method="post" {S_FORM_ENCTYPE}>
					<div class="title">{L_POST_A}</div>
<!-- BEGIN switch_username_select -->
					<div class="module">
						<div id="username">用户名：</div>
						<div id="input-username"><input type="text" name="username" /></div>
					</div>
<!-- END switch_username_select -->
<!-- BEGIN switch_allow_subject_on -->
					<div class="module">
						<div id="subject">主题标题：</div>
						<div id="input-subject"><input type="text" name="subject" value="{SUBJECT}" /></div>
					</div>
<!-- END switch_allow_subject_on -->
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
						<div id="textarea-message"><textarea id="text" name="message" rows="5" style="width: 99%;">{MESSAGE}</textarea></div>
					</div>
<!-- BEGIN switch_type_toggle -->
					<div class="module">{S_TYPE_TOGGLE}</div>
<!-- END switch_type_toggle -->
<!-- BEGIN switch_allow_marrow_on -->
					<div class="module">
						<div id="chekbox-marrow">
							<input type="checkbox" name="marrow" {S_MARROW_CHECKED} /> 设为精华
						</div>
					</div>	
<!-- END switch_allow_marrow_on -->
<!-- BEGIN switch_notify_checkbox -->
					<div class="module">
						<div id="chekbox-notify">
							<input type="checkbox" name="notify" {S_NOTIFY_CHECKED} /> 有人回复该主题通知我
						</div>
					</div>
<!-- END switch_notify_checkbox -->
<!-- BEGIN switch_delete_checkbox -->
					<div class="module">
						<div id="chechbox-delete">
							<input type="checkbox" name="delete" /> 删除
						</div>
					</div>
<!-- END switch_delete_checkbox -->
					{POLLBOX}
					{ATTACHBOX}
<!-- BEGIN switch_confirm -->
					<div class="module">
						<div id="confirm-img">验证码：</div>
						<div id="img-confirm-img">{CONFIRM_IMG}</div>
						<div id="input-confirm-img"><input type="text" name="confirm_code" value="" /></div>
					</div>
<!-- END switch_confirm -->
					<div class="title">说明</div>
					<div class="module">
						<div id="help">
							<div id="help-smiles"><a href="{SMILES_TABLE}">表情的使用</a></div>
							<div id="help-bbcode"><a href="{BBCODE_TABLE}">BBCode的使用</a></div>
							<div id="help-attach"><a href="{ATTACH_TABLE}">附件的使用</a></div>
						</div>
					</div>
				
					{S_HIDDEN_FORM_FIELDS}
					<div class="center">
						<div id="input-post"><input type="submit" name="post" value=" 提交 " /></div>
					</div>
				</form>
				<div class="nav">
					<a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a>
				</div>
			</div>