			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;<a href="{U_FORUM}">论坛中心</a>&gt;<a href="{U_VIEW_FORUM}">{FORUM_NAME}</a>&gt;查看帖子</div>
				{POLL_DISPLAY}
				<h1 class="title"><strong>{TOPIC_MARROW}</strong><span>{TOPIC_TITLE}</span>[{TOPIC_VIEWS}阅/{TOPIC_REPLIES}回]</h1>
<!-- BEGIN postrow -->
				<div class="box {postrow.ROW_CLASS} module" id="{postrow.U_POST_ID}">
					<div class="nav-post">
						<div class="left">
							<div class="poster-info">
								【<strong>{postrow.NOMER_POSTA}】</strong>
								{postrow.USER_LEVEL}
								<b>{postrow.POSTER_NAME}</b>
								{postrow.POSTER_ONLINE_STATUS}
							</div>
							<div class="poster-info">
								ＩＤ：{postrow.POSTER_ID} , 等级：{postrow.RANK_IMAGE}{postrow.POSTER_RANK}
								{postrow.DOWNLOAD_TOPIC} 
							</div>
						</div>
						<div class="right">
							{postrow.AVATAR_IMG}
						</div>
					</div>
					<div class="topic-manage">
						{postrow.S_TOPIC_ADMIN}
						{postrow.POSTER_INFO}
						[时间]：{postrow.POST_DATE}<br />
						[操作]：{postrow.REPLY}{postrow.QUOTE}{postrow.EDIT}{postrow.DELETE}{postrow.IP}<br />
						{postrow.CLASS_VIEW}
						{postrow.CLASS_SELECT}
					</div>
					<div class="module row_text">
						{postrow.MESSAGE}
						{postrow.ATTACHMENTS}
						<p>{postrow.EDITED_MESSAGE}</p>
						{postrow.TOPIC_CLOSED}
						<p>{postrow.SIGNATURE}</p>
					</div>
					
				</div>
<!-- END postrow -->
				{PAGINATION}
					{POSTTOPIC}
<!-- BEGIN not_auth_reply -->
					<div>您没有权限回复这个帖子</div>
<!-- END not_auth_reply -->
<!-- BEGIN auth_reply_login -->
					<div>您没有权限回复这个帖子，请先 <a href="{auth_reply_login.U_LOGIN}">登录</a> / <a href="{auth_reply_login.U_REGISTAR}">注册</a> 吧！</div>
<!-- END auth_reply_login -->
					{S_WATCH_TOPIC}
			</div>
			
			<script src="./styles/Gray/js/jquery.min.js"></script>
			<script src="./styles/Gray/js/jQuery.imgAutoSize.js"></script>
			<script type="text/javascript">
			jQuery(function ($) {
				$('.attachImg').imgAutoSize();
			});
			function selectCode(a)
			{
				// Get ID of code block
				var e = a.parentNode.parentNode.getElementsByTagName('CODE')[0];

				// Not IE
				if (window.getSelection)
				{
					var s = window.getSelection();
					// Safari
					if (s.setBaseAndExtent)
					{
						s.setBaseAndExtent(e, 0, e, e.innerText.length - 1);
					}
					// Firefox and Opera
					else
					{
						// workaround for bug # 42885
						if (window.opera && e.innerHTML.substring(e.innerHTML.length - 4) == '<BR>')
						{
							e.innerHTML = e.innerHTML + '&nbsp;';
						}

						var r = document.createRange();
						r.selectNodeContents(e);
						s.removeAllRanges();
						s.addRange(r);
					}
				}
				// Some older browsers
				else if (document.getSelection)
				{
					var s = document.getSelection();
					var r = document.createRange();
					r.selectNodeContents(e);
					s.removeAllRanges();
					s.addRange(r);
				}
				// IE
				else if (document.selection)
				{
					var r = document.body.createTextRange();
					r.moveToElementText(e);
					r.select();
				}
			}
<!-- BEGIN user_otv -->
			function otv(u) {
				var t = document.getElementById('text');
					t.value  += u + ', ';
				var end = t.value.length; 
				t.setSelectionRange(end,end); 
				t.focus(); 
			}
<!-- END user_otv -->
<!-- BEGIN bb_panel -->
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
<!-- END bb_panel -->
			</script>