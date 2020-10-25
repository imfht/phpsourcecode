			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级管理面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;配置</div>
				<p>在这里您可以调整一些网站的一些设置</p>
				<form action="{S_CONFIG_ACTION}" method="post">
					<div class="title">网站配置</div>
					<div class="module bm-gray">
						<label>服务器地址：</label>
						<div><input type="text" maxlength="255" name="server_name" value="{SERVER_NAME}" /></div>
					<div>
					<div class="module bm-gray">
						<label>服务器端口：</label>
						<div><input type="text" maxlength="5" size="5" name="server_port" value="{SERVER_PORT}" /></div>
					</div>
					<div class="module bm-gray">
						<label>脚本路径：</label>
						<div><input type="text" maxlength="255" name="script_path" value="{SCRIPT_PATH}" /></div>
					</div>
					<div class="module bm-gray">
						<label>网站名称：</label>
						<div><input type="text" size="25" maxlength="100" name="sitename" value="{SITENAME}" /></div>
					</div>
					<div class="module bm-gray">
						<label>网站副标题：</label>
						<div><input type="text" maxlength="255" name="site_desc" value="{SITE_DESCRIPTION}" /></div>
					</div>
					<div class="module bm-gray">
						<label>备案号：</label>
						<div><input type="text" maxlength="255" name="beian_info" value="{BEIAN_INFO}" /></div>
					</div>					
					<div class="module bm-gray">
						<label>首页公告：</label>
						<div><textarea name="index_announcement" rows="5" style="width: 99%;">{INDEX_ANNOUNCEMENT}</textarea></div>
					</div>
					<div class="module bm-gray">
						<label>网站总开关：</label>
						<p>在关闭网站期间请不要退出您的帐号，否则您无法再次登录网站！</p>
						<div><input type="radio" name="board_disable" value="1" {S_DISABLE_BOARD_YES} /> 关闭</div>
						<div><input type="radio" name="board_disable" value="0" {S_DISABLE_BOARD_NO} /> 打开</div>
					</div>
					<div class="module bm-gray">
						<label>伪静态：</label>
						<p>如果您的主机不支持 Output Control 系列函数千万别开启，否则您的网站会变成空白的哦！</p>
						<div><input type="radio" name="open_rewrite" value="1" {REWRITE_YES} /> 开启</div>
						<div><input type="radio" name="open_rewrite" value="0" {REWRITE_NO} /> 关闭</div>
					</div>
					<div class="module bm-gray">
						<label>展开论坛页面的论坛栏目：</label>
						<div><input type="radio" name="index_spisok" value="1" {SPISOK_YES} /> 展开</div>
						<div><input type="radio" name="index_spisok" value="0" {SPISOK_NO} /> 收起</div>
					</div>
					<div class="module bm-gray">
						<label>会员激活方法：</label>
						<p>激活需要使用SMTP发送邮件，如果【E-mail 设置】没有配置正确，用户是无法收到激活邮件的，如果你不懂什么是SMTP建议保持“关闭状态”</p>
						<div><input type="radio" name="require_activation" value="{ACTIVATION_NONE}" {ACTIVATION_NONE_CHECKED} /> 关闭状态</div>
						<div><input type="radio" name="require_activation" value="{ACTIVATION_USER}" {ACTIVATION_USER_CHECKED} /> 用户可以自己激活</div>
						<div><input type="radio" name="require_activation" value="{ACTIVATION_ADMIN}" {ACTIVATION_ADMIN_CHECKED} /> 用户激活后须通过超级管理员审核</div>
					</div>
					<div class="module bm-gray">
						<label>注册图形验证码：</label>
						<p>图形验证码需要PHP GD库的支持</p>
						<div><input type="radio" name="enable_confirm" value="1" {CONFIRM_ENABLE} /> 开启</div>
						<div><input type="radio" name="enable_confirm" value="0" {CONFIRM_DISABLE} /> 关闭</div>
					</div> 
					<div class="module bm-gray">
						<label>在线发送E-mail：</label>
						<p>用户之间在线发送邮件的功能，如果【E-mail 设置】没有配置正确，建议保持设置为“关闭”</p>
						<div><input type="radio" name="board_email_form" value="1" {BOARD_EMAIL_FORM_ENABLE} /> 开启</div>
						<div><input type="radio" name="board_email_form" value="0" {BOARD_EMAIL_FORM_DISABLE} /> 关闭</div>
					</div>
					<div class="module bm-gray">
						<label>默认时间格式：</label>
						<div><input type="text" name="default_dateformat" value="{DEFAULT_DATEFORMAT}" /></div>
					</div>
					<div class="module bm-gray">
						<label>默认时区：</label>
						<div>{TIMEZONE_SELECT}</div>
					</div>
					<div class="module bm-gray">
						<label>默认风格：</label>
						<div>{STYLE_SELECT}</div>
					</div>
					<div class="module bm-gray">
						<label>在线统计时间：</label>
						<p>在该时间段内都显示为在线用户（单位/秒）</p>
						<div><input type="text" size="3" maxlength="4" name="online_time" value="{ONLINE_TIME}" /></div>
					</div>
					<div class="module bm-gray">
						<label>用户可以设置的最大年龄限制：</label>
						<div><input type="text" size="4" maxlength="4" name="max_user_age" value="{MAX_USER_AGE}" /></div>
					</div>
					<div class="module bm-gray">
						<label>用户可以设置的最小年龄限制：</label>
						<div><input type="text" size="4" maxlength="4" name="min_user_age" value="{MIN_USER_AGE}" /></div>
					</div>
					<div class="module bm-gray">
						<label>搜索时间间隔（单位/秒）：</label>
						<div><input type="text" size="3" maxlength="4" name="search_flood_interval" value="{SEARCH_FLOOD_INTERVAL}" /></div>
					</div>
					<div class="module bm-gray">
						<label>密码错误次数限制（0为关闭）：</label>
						<div><input type="text" size="3" maxlength="4" name="max_login_attempts" value="{MAX_LOGIN_ATTEMPTS}" /></div>
					</div>
					<div class="module bm-gray">
						<label>密码错误次数限制重置时间（单位／分钟）：</label>
						<div><input type="text" size="3" maxlength="4" name="login_reset_time" value="{LOGIN_RESET_TIME}" /></div>
					</div>
					<div class="module bm-gray">
						<label>注册等待时间（单位/分钟）：</label>
						<p>用户注册成功后需要等待一段时间才可以登录网站（0为关闭）</p>
						<div><input type="text" size="3" maxlength="4" name="min_login_regdate" value="{MIN_LOGIN_REGDATE}" /></div>
					</div>
					<div class="module bm-gray">
						<label>表情的存放目录：</label>
						<p>相对于您的程序安装目录</p>
						<div><input type="text" size="20" maxlength="255" name="smilies_path" value="{SMILIES_PATH}" /></div>
					</div>
					<div class="module bm-gray">
						<label>允许更改用户名：</label>
						<div><input type="radio" name="allow_namechange" value="1" {NAMECHANGE_YES} /> 是</div>
						<div><input type="radio" name="allow_namechange" value="0" {NAMECHANGE_NO} /> 否</div>
					</div>
					<div class="title">论坛配置</div>
					<div class="module bm-gray">
						<label>主题/帖子发表间隔时间（单位/秒）：</label>
						<div><input type="text" size="3" maxlength="4" name="flood_interval" value="{FLOOD_INTERVAL}" /></div>
					</div>
					<div class="module bm-gray">
						<label>每次发表主题/帖子需要输入验证码：</label>
						<div><input type="radio" name="captcha_in_topic" value="1" {CAPTCHA_IN_TOPIC_YES} /> 是</div>
						<div><input type="radio" name="captcha_in_topic" value="0" {CAPTCHA_IN_TOPIC_NO} /> 否</div>
					</div>
					<div class="module bm-gray">
						<label>主题/帖子中的【引用】功能：</label>
						<div><input type="radio" name="message_quote" value="1" {MESSAGE_QUOTE_YES} /> 显示</div>
						<div><input type="radio" name="message_quote" value="0" {MESSAGE_QUOTE_NO} /> 隐藏</div>
					</div>
					<div class="module bm-gray">
						<label>快速回复：</label>
						<p>在主题页面的底部显示一个快速回复的输入框</p>
						<div><input type="radio" name="quick_answer" value="0" {QUICK_ANSWER_OFF} /> 关闭</div>
						<div><input type="radio" name="quick_answer" value="1" {QUICK_ANSWER_ON} /> 开启</div>
						<div><input type="radio" name="quick_answer" value="2" {QUICK_ANSWER_USER} /> 由用户选择</div>						
					</div>					
					<div class="module bm-gray">
						<label>论坛中每页显示多少个主题：</label>
						<div><input type="text" name="topics_per_page" size="3" maxlength="4" value="{TOPICS_PER_PAGE}" /></div>
					</div>

					<div class="module bm-gray">
						<label>主题中每页显示多少帖子：</label>
						<div><input type="text" name="posts_per_page" size="3" maxlength="4" value="{POSTS_PER_PAGE}" /></div>
					</div>
					<div class="module bm-gray">
						<label>用户中心论坛分页限制：</label>
						<p>在用户个人设置面板中“主题中每页显示多少帖子”选项设置的最高限制</p>
						<div><input type="text" name="max_user_topics_per_page" size="3" maxlength="4" value="{MAX_USER_TOPICS_PER_PAGE}" /></div>
					</div>
					<div class="module bm-gray">
						<label>用户中心主题分页限制：</label>
						<p>在用户个人设置面板中“主题中每页显示多少帖子”选项设置的最高限制</p>
						<div><input type="text" name="max_user_posts_per_page" size="3" maxlength="4" value="{MAX_USER_POSTS_PER_PAGE}" /></div>
					</div>
					<div class="module bm-gray">
						<label>论坛主题自动清理：</label>
						<div><input type="radio" name="prune_enable" value="1" {PRUNE_YES} /> 开启</div>
						<div><input type="radio" name="prune_enable" value="0" {PRUNE_NO} /> 关闭</div>
					</div>
					<div class="module bm-gray">
						<label>允许匿名用户留言：</label>
						<div><input type="radio" name="allow_guests_gb" value="1" {NO_GUEST_YES} /> 是</div>
						<div><input type="radio" name="allow_guests_gb" value="0" {NO_GUEST_NO} /> 否</div>
					</div>
					<div class="module bm-gray">
						<label>是否允许给自己留言：</label>
						<div><input type="radio" name="gb_quick" value="1" {GB_QUICK_YES} /> 是</div>
						<div><input type="radio" name="gb_quick" value="0" {GB_QUICK_NO} /> 否</div>
					</div>
					<div class="module bm-gray">
						<label>留言板中每页显示多少条留言：</label>
						<div><input type="text" name="gb_posts" value="{GB_POST}" /></div>
					</div>
					<div class="module bm-gray">
						<label>帖子的最后回复时间：</label>
						<div><input type="radio" name="posl_red" value="1" {POSL_YES} /> 显示</div>
						<div><input type="radio" name="posl_red" value="0" {POSL_NO} /> 隐藏</div>
					</div>
					<div class="module bm-gray">
						<label>用户最多可以建立多少个投票选项：</label>
						<div><input type="text" name="max_poll_options" size="4" maxlength="4" value="{MAX_POLL_OPTIONS}" /></div>
					</div>
					<div class="module bm-gray">
						<label>允许用户使用 BBCode 标签：</label>
						<div><input type="radio" name="allow_bbcode" value="1" {BBCODE_YES} /> 是</div>
						<div><input type="radio" name="allow_bbcode" value="0" {BBCODE_NO} /> 否</div>
					</div>
					<div class="module bm-gray">
						<label>允许用户使用 表情 标签：</label>
						<div><input type="radio" name="allow_smilies" value="1" {SMILE_YES} /> 是</div>
						<div><input type="radio" name="allow_smilies" value="0" {SMILE_NO} /> 否</div>
					</div>
					<div class="module bm-gray">
						<label>允许用户使用签名：</label>
						<div><input type="radio" name="allow_sig" value="1" {SIG_YES} /> 是</div>
						<div><input type="radio" name="allow_sig" value="0" {SIG_NO} /> 否</div>
					</div>
					<div class="module bm-gray">
						<label>附带签名的长度限制（单位/字节）：</label>
						<div><input type="text" size="5" maxlength="4" name="max_sig_chars" value="{SIG_SIZE}" /></div>
					</div>
					<div class="module bm-gray">
						<label>每个帖子可以使用多少个表情：</label>
						<div><input type="text" maxlength="4" size="4" name="max_smiles_in_message" value="{MAX_SMILES}" /></div>
					</div>
					<div class="title">Cookie 设置</div>
					<p>这些设定控制着 Cookie 的定义，就一般的情况，使用系统预设值就可以了。如果您要更改这些设定，请谨慎设定，不当的设定将影响用户的登陆 </p>
					<div class="module bm-gray">
						<label>Cookie 域名：</label>
						<div><input type="text" maxlength="255" name="cookie_domain" value="{COOKIE_DOMAIN}" /></div>
					</div>
					<div class="module bm-gray">
						<label>Cookie 名称：</label>
						<div><input type="text" maxlength="16" name="cookie_name" value="{COOKIE_NAME}" /></div>
					</div>
					<div class="module bm-gray">
						<label>Cookie 路径：</label>
						<div><input type="text" maxlength="255" name="cookie_path" value="{COOKIE_PATH}" /></div>
					</div>
					<div class="module bm-gray">
						<label>Cookie 加密：</label>
						<p>如果您的服务器运行于 SSL 方式请设置为开启，否则请设置为关闭</p>
						<div><input type="radio" name="cookie_secure" value="0" {S_COOKIE_SECURE_DISABLED} /> 开启</div>
 						<div><input type="radio" name="cookie_secure" value="1" {S_COOKIE_SECURE_ENABLED} /> 关闭</div>
					</div>
					<div class="module bm-gray">
						<label>Session 有效期（单位/秒）：</label>
						<div><input type="text" maxlength="5" size="5" name="session_length" value="{SESSION_LENGTH}" />
					</div>
					<div class="title">站内信息</div>
					<div class="module bm-gray">
						<label>站内信息开关：</label>
						<div><input type="radio" name="privmsg_disable" value="0" {S_PRIVMSG_ENABLED} /> 开</div>
						<div><input type="radio" name="privmsg_disable" value="1" {S_PRIVMSG_DISABLED} /> 关</div>
					</div>
					<div class="module bm-gray">
						<label>收信箱 最大容量：</label>
						<div><input type="text" maxlength="4" size="4" name="max_inbox_privmsgs" value="{INBOX_LIMIT}" /></div>
					</div>
					<div class="module bm-gray">
						<label>发信箱 最大容量：</label>
						<div><input type="text" maxlength="4" size="4" name="max_sentbox_privmsgs" value="{SENTBOX_LIMIT}" /></div>
					</div>
					<div class="module bm-gray">
						<label>草稿箱 最大容量：</label>
						<div><input type="text" maxlength="4" size="4" name="max_savebox_privmsgs" value="{SAVEBOX_LIMIT}" /></div>
					</div>
					<div class="title">头像设置</div>
					<div class="module bm-gray">
						<label>允许链接外站图片作为头像：</label>
						<div><input type="radio" name="allow_avatar_remote" value="1" {AVATARS_REMOTE_YES} /> 是</div>
						<div><input type="radio" name="allow_avatar_remote" value="0" {AVATARS_REMOTE_NO} /> 否</div>
					</div>
					<div class="module bm-gray">
						<label>允许用户上传头像：</label>
						<div><input type="radio" name="allow_avatar_upload" value="1" {AVATARS_UPLOAD_YES} /> 是</div>
						<div><input type="radio" name="allow_avatar_upload" value="0" {AVATARS_UPLOAD_NO} /> 否</div>
					</div>
					<div class="module bm-gray">
						<label>上传头像文件大小限制：</label>
						<div><input type="text" size="4" maxlength="10" name="avatar_filesize" value="{AVATAR_FILESIZE}" /> Bytes</div>
					</div>
					<div class="module bm-gray">
						<label>头像像素限制：</label>
						<p>像素高 x 像素宽</p>
						<div>
							<input type="text" size="3" maxlength="4" name="avatar_max_height" value="{AVATAR_MAX_HEIGHT}" />
							x 
							<input type="text" size="3" maxlength="4" name="avatar_max_width" value="{AVATAR_MAX_WIDTH}" />
						</div>
					</div>
					<div class="module bm-gray">
						<label>头像存放目录：</label>
						<div><input type="text" size="20" maxlength="255" name="avatar_path" value="{AVATAR_PATH}" /></div>
					</div>
					<div class="title">E-mail 设置</div>
					<div class="module bm-gray">
						<label>SMTP发送邮件功能：</label>
						<p>此选项为 开启 时下面的设置才会生效</p>
						<div><input type="radio" name="smtp_delivery" value="1" {SMTP_YES} /> 开启</div>
						<div><input type="radio" name="smtp_delivery" value="0" {SMTP_NO} /> 关闭</div>
					</div>
					<div class="module bm-gray">
						<label>系统 E-mail：</label>
						<div><input type="text" size="25" maxlength="100" name="board_email" value="{EMAIL_FROM}" /></div>
					</div>
					<div class="module bm-gray">
						<label>SMTP 服务器：</label>
						<div><input type="text" name="smtp_host" value="{SMTP_HOST}" size="25" maxlength="50" /></div>
					</div>
					<div class="module bm-gray">
						<label>SMTP 用户名：</label>
						<p>只有您的 SMTP 服务器要求用户时才填写这个选项</p>
						<div><input type="text" name="smtp_username" value="{SMTP_USERNAME}" size="25" maxlength="255" /></div>
					</div>
					<div class="module bm-gray">
						<label>SMTP 密码：</label>
						<p>只有您的 SMTP 服务器要求用户时才填写这个选项</p>
						<div><input type="password" name="smtp_password" value="{SMTP_PASSWORD}" size="25" maxlength="255" /></div>
					</div>
					<div class="module">
						<label>E-mail 签名：</label>
						<p>这个签名档将会被附加在所有由论坛系统发出的电子邮件中</p>
						<div><textarea name="board_email_sig" rows="5" style="width: 98%;">{EMAIL_SIG}</textarea></div>
					</div>
					{S_HIDDEN_FIELDS} 
					<div><input type="submit" name="submit" value="提交" /></div>
				</form>
			</div>