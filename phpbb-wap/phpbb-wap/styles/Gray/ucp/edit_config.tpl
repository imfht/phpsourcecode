			<div id="main">
				{ERROR_BOX}
				<form action="{S_PROFILE_ACTION}" {S_FORM_ENCTYPE} method="post">
					<div class="title"><a href="{U_PROFILE}">资料卡</a> | 我的设置</div>
					<div class="module clear">
						<div class="left"><strong>我的地盘中的邮件地址</strong></div>
						<div class="right">
							<input type="radio" name="viewemail" value="1" {VIEW_EMAIL_YES} /> 显示<br />
							<input type="radio" name="viewemail" value="0" {VIEW_EMAIL_NO} /> 隐藏
						</div>
					</div>
					<div class="module clear">
						<div class="left"><strong>我的在线状态：</strong></div>
						<div class="right">
							<input type="radio" name="hideonline" value="0" {HIDE_USER_NO} /> 显示<br />
							<input type="radio" name="hideonline" value="1" {HIDE_USER_YES} /> 隐藏
							
						</div>
					</div>
					<div class="module clear">
						<div class="left"><strong>把我的帐号设置为隐身状态：</strong></div>
						<div class="right">
							<input type="radio" name="on_off" value="1" {ON_OFF_YES} /> 是<br />
							<input type="radio" name="on_off" value="0" {ON_OFF_NO} /> 否
						</div>
					</div>
					<div class="module clear">
						<div class="left"><strong>在主题浏览页面中的快速回复主题的输入框：</strong></div>
						<div class="right">
							<input type="radio" name="quick_answer" value="1" {QUICK_ANSWER_YES} /> 显示<br />
							<input type="radio" name="quick_answer" value="0" {QUICK_ANSWER_NO} /> 隐藏
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>BBCode快速输入面板：</strong>
							<p>“在主题浏览页面中的快速回复主题的输入框” 必须为显示</p>
							<p>您的浏览器支持 Javascript </p>
						</div>
						<div class="right">
							<input type="radio" name="bb_panel" value="1" {BB_PANEL_YES} /> 是<br />
							<input type="radio" name="bb_panel" value="0" {BB_PANEL_NO} /> 否
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>快捷回复帖子：</strong>
							<p>帖子浏览页面中点击“回复”链接可以把用户名输入到回复框中</p>
							<p>在主题浏览页面中的快速回复主题的输入框 必须为显示</p>
							<p>您的浏览器支持 Javascript </p>
						</div>
						<div class="right">
							<input type="radio" name="java_otv" value="1" {JAVA_OTV_YES} /> 开启<br />
							<input type="radio" name="java_otv" value="0" {JAVA_OTV_NO} /> 关闭
						</div>
					</div>
					<!-- BEGIN switch_message_quote -->
					<div class="module clear">
						<div class="left">
							<strong>快捷引用帖子：</strong>
							<p>帖子浏览页面中点击“引用”链接可以把帖子输入到回复框中</p>
							<p>在主题浏览页面中的快速回复主题的输入框 必须为显示</p>
							<p>您的浏览器支持 Javascript </p>
						</div>
						<div class="right">
							<input type="radio" name="message_quote" value="1" {MESSAGE_QUOTE_YES} /> 开启<br />
							<input type="radio" name="message_quote" value="0" {MESSAGE_QUOTE_NO} /> 关闭
						</div>
					</div>
					<!-- END switch_message_quote -->
					<div class="module clear">
						<div class="left">
							<strong>论坛中的分类</strong>
						</div>
						<div class="right">
							<input type="radio" name="index_spisok" value="1" {INDEX_SPISOK_YES} /> 展开<br />
							<input type="radio" name="index_spisok" value="0" {INDEX_SPISOK_NO} /> 收起
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>主题/帖子的最后编辑时间</strong>
						</div>
						<div class="right">
							<input type="radio" name="posl_red" value="1" {POSL_RED_YES} /> 显示<br />
							<input type="radio" name="posl_red" value="0" {POSL_RED_NO} /> 关闭
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>我发表的主题有回复时用站内信息通知我</strong>
						</div>
						<div class="right">
							<input type="radio" name="notifypm" value="1" {NOTIFY_PM_YES} /> 通知<br />
							<input type="radio" name="notifypm" value="0" {NOTIFY_PM_NO} /> 忽略</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>回复通知发送到</strong>
						</div>
						<div class="right">
							<input type="checkbox" name="notifyreply_to_pm"{NOTIFY_REPLY_TO_PM} /> 站内收信箱<br />
							<input type="checkbox" name="notifyreply_to_email"{NOTIFY_REPLY_TO_EMAIL} /> E-mail 邮箱
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>是否开通用户中心的留言板功能</strong>
						</div>
						<div class="right">
							<input type="radio" name="gb_can" value="1" {GB_CAN_YES} /> 是<br />
							<input type="radio" name="gb_can" value="0" {GB_CAN_NO} /> 否
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>我的风格</strong>
						</div>
						<div class="right">
							{STYLE_SELECT}
						</div>
					</div>					
					<div class="module clear">
						<div class="left">
							<strong>我所在的时区</strong>
						</div>
						<div class="right">
							{TIMEZONE_SELECT}
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>时间/日期显示的格式</strong>
						</div>
						<div class="right">
							{DATE_FORMAT}
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>论坛版块中每页显示多少个主题？</strong>
						</div>
						<div class="right">
							<input type="text" name="topics_per_page" value="{TOPICS_PER_PAGE}" size="2" maxlength="3" />
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>主题中显示多少个帖子？</strong>
						</div>
						<div class="right">
							<input type="text" name="posts_per_page" value="{POSTS_PER_PAGE}" size="2" maxlength="3" />
						</div>
					</div>
					<div class="module clear">
						<div class="left">
							<strong>帖子文字超度最多显示多长？</strong>
							<p>超出部分显示 <a href="#">--&gt;</a>，点击后可以显示全部</p>
						</div>
						<div class="right">
							<input type="text" name="post_leng" value="{POST_LENG}" size="2" maxlength="8" />
						</div>
					</div>
<!-- BEGIN switch_avatar_block -->
					<div class="module clear">
						<div class="left">
							<strong>头像</strong>
							<p>{L_AVATAR_EXPLAIN}</p>
							<p>您可以从本地上传头像图片</p>
							<p>也可以使用URL上传</p>
							<p>甚至可以使用一个链接作为头像</p>
						</div>
						<div class="right">{AVATAR}</div>
					</div>
	<!-- BEGIN switch_avatar_local_upload -->
					<div class="module clear">
						<div class="left"><strong>本地上传</strong></div>
						<div class="right">
							<input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" />
							<input type="file" name="avatar" />
						</div>
					</div>
	<!-- END switch_avatar_local_upload -->
					
	<!-- BEGIN switch_avatar_remote_upload -->
					<div class="module clear">
						<div class="left"><strong>URL上传</strong></div>
						<div class="right"><input type="text" name="avatarurl" /></div>
					</div>
	<!-- END switch_avatar_remote_upload -->
	<!-- BEGIN switch_avatar_remote_link -->
					<div class="module clear">
						<div class="left"><strong>链接头像</strong></div>
						<div class="right"><input type="text" name="avatarremoteurl"/></div>
					</div>
	<!-- END switch_avatar_remote_link -->
<!-- END switch_avatar_block -->
					<div class="module clear">
						<div class="left"><strong>ＱＱ互联登录</strong></div>
						<div class="right">{QQ_LOGIN}</div>
					</div>
					{S_HIDDEN_FIELDS}
					<div class="center">
						<br />
						<input class="button" type="submit" name="submit" value="保存修改" />
						<br />
						&nbsp;
					</div>
				</form>
				<div class="nav"><a href="{U_PROFILE}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>