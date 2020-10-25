			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_USERS_ADMIN}">选择用户</a>&gt;用户信息</div>
				{ERROR_BOX}
				<form action="{S_PROFILE_ACTION}" {S_FORM_ENCTYPE} method="post">
				<div class="title">基本信息</div>
				<div class="module">
					<label>用户名：</label>
					<div><input type="text" name="username" maxlength="40" value="{USERNAME}" /></div>
				</div>
				<div class="module">
					<label>电子邮箱：</label>
					<div><input type="text" name="email" maxlength="255" value="{EMAIL}" /></div>
				</div>
				<div class="module">
					<label>新密码：</label>
					<p>如果您不希望更改改用户的密码请留空</p>
					<input type="password" name="password" maxlength="32" value="" /></div>
				</div>
				<div class="module">
					<label>确认新密码：</label>
					<div><input type="password" name="password_confirm" maxlength="32" value="" /></div>
				</div>
				<div class="title">用户中心信息</div>
				<div class="module">
					<label>ＱＱ号码：</label>
					<div><input type="text" name="qq" maxlength="15" value="{QQ}" /></div>
				</div>
				<div class="module">
					<label>电话号码：</label>
					<div><input type="text" name="number" maxlength="15" value="{NUMBER}" /></div>
				</div>
				<div class="module">
					<label>AIM帐号：</label>
					<div><input type="text" name="aim" maxlength="255" value="{AIM}" /></div>
				</div>
				<div class="module">
					<label>MSN帐号：</label>
					<div><input type="text" name="msn" maxlength="255" value="{MSN}" /></div>
				</div>
				<div class="module">
					<label>雅虎帐号：</label>
					<div><input type="text" name="yim" maxlength="255" value="{YIM}" /></div>
				</div>
				<div class="module">
					<label>个人博客：</label>
					<div><input type="text" name="website" maxlength="255" value="{WEBSITE}" /></div>
				</div>
				<div class="module">
					<label>用户签名：</label>
					<p>225个字符以内</p>
					<div><input type="text" name="signature" maxlength="255" value="{SIGNATURE}" /></div>
				</div>
				<div class="module">
					<label>所在地：</label>
					<div><input type="text" name="location" maxlength="100" value="{LOCATION}" /></div>
				</div>
				<div class="module">
					<label>职业：</label>
					<div><input type="text" name="occupation" maxlength="100" value="{OCCUPATION}" /></div>
				</div>
				<div class="module">
					<label>兴趣爱好：</label>
					<div><input type="text" name="interests" maxlength="150" value="{INTERESTS}" /></div>
				</div>
				<div class="module">
					<label>生日：</label>
					{S_BIRTHDAY}
				</div>
				<div class="module">
					<label>性别：</label>
					<div>
						<input type="radio" name="gender" value="0" {GENDER_NO_SPECIFY_CHECKED}/> 保密
						<input type="radio" name="gender" value="1" {GENDER_MALE_CHECKED}/> 男
						<input type="radio" name="gender" value="2" {GENDER_FEMALE_CHECKED}/> 女
					</div>
				</div> 
				<div class="title">个人中心设置</div>
				<div class="module">
					<label>显示邮件地址：</label>
					<div>
						<input type="radio" name="viewemail" value="1" {VIEW_EMAIL_YES} /> 是
						<input type="radio" name="viewemail" value="0" {VIEW_EMAIL_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>隐藏在线状态：</label>
					<div>
						<input type="radio" name="hideonline" value="1" {HIDE_USER_YES} /> 是
						<input type="radio" name="hideonline" value="0" {HIDE_USER_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>帖子回复通知：</label>
					<div>
						<input type="radio" name="notifypm" value="1" {NOTIFY_PM_YES} /> 是
						<input type="radio" name="notifypm" value="0" {NOTIFY_PM_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>允许他人对该用户进行留言：</label>
					<div>
						<input type="radio" name="gb_can" value="1" {GB_CAN_YES} /> 是
						<input type="radio" name="gb_can" value="0" {GB_CAN_NO} /> 否
					</div>
				</div>				
				<div class="module">
					<label>回复的帖子通知到：</label>
					<div><input type="checkbox" name="notifyreply_to_pm"{NOTIFY_REPLY_TO_PM} /> 用信息通知</div>
					<div><input type="checkbox" name="notifyreply_to_email"{NOTIFY_REPLY_TO_EMAIL} /> 用邮件通知</div>
				</div>
				<div class="module">
					<label>发表帖子允许使用签名：</label>
					<div>
						<input type="radio" name="attachsig" value="1" {ALWAYS_ADD_SIGNATURE_YES} /> 是
						<input type="radio" name="attachsig" value="0" {ALWAYS_ADD_SIGNATURE_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>发表体诶允许使用BBCode：</label>
					<div>
						<input type="radio" name="allowbbcode" value="1" {ALWAYS_ALLOW_BBCODE_YES} /> 是
						<input type="radio" name="allowbbcode" value="0" {ALWAYS_ALLOW_BBCODE_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>发表帖子允许使用HTML：</label>
					<div>
						<input type="radio" name="allowhtml" value="1" {ALWAYS_ALLOW_HTML_YES} /> 是
						<input type="radio" name="allowhtml" value="0" {ALWAYS_ALLOW_HTML_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>发表帖子允许使用表情：</label>
					<div>
						<input type="radio" name="allowsmilies" value="1" {ALWAYS_ALLOW_SMILIES_YES} /> 是
						<input type="radio" name="allowsmilies" value="0" {ALWAYS_ALLOW_SMILIES_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>用户名颜色：</label>
					<div><input type="text" name="nic_color" maxlength="10" value="{NIC_COLOR}" /></div>
				</div>
				<div class="module">
					<label>时区：</label>
					<div>{TIMEZONE_SELECT}</div>
				</div>
				<div class="module">
					<label>日期显示格式：</label>
					<div><input type="text" name="dateformat" value="{DATE_FORMAT}" maxlength="16" /></div>
				</div>
				<div class="module">
					<label>论坛中每页显示主题数量：</label>
					<div><input type="text" name="topics_per_page" value="{TOPICS_PER_PAGE}" size="5" maxlength="3" /></div>
				</div>
				<div class="module">
					<label>帖子中每页显示帖子数量：</label>
					<div><input type="text" name="posts_per_page" value="{POSTS_PER_PAGE}" size="5" maxlength="3" /></div>
				</div>
				<div class="title">头像设置</div>
				<div class="module">
					{AVATAR}
					<div><input type="checkbox" name="avatardel" /> 删除头像</div>
				</div>
<!-- BEGIN avatar_local_upload -->
				<div class="module">
					<label>本地上传头像：</label>
					<div>
						<input type="hidden" name="MAX_FILE_SIZE" value="{AVATAR_SIZE}" />
						<input type="file" name="avatar" />
					</div>
				</div>
<!-- END avatar_local_upload -->
<!-- BEGIN avatar_remote_upload -->
				<div class="module">
					<label>从 URL 上传头像：</label>
					<div><input type="text" name="avatarurl" /></div>
				</div>
<!-- END avatar_remote_upload -->
<!-- BEGIN avatar_remote_link -->
				<div class="module">
					<label>链接头像：</label>
					<div><input type="text" name="avatarremoteurl" /></div>
				</div>
<!-- END avatar_remote_link -->
				<div class="title">其它设置</div>
				<div class="module">
					<label>文件上传限制：</label>
					<div>{S_SELECT_UPLOAD_QUOTA}</div>
				</div>
				<div class="module">
					<label>私人信息限制：</label>
					<div>{S_SELECT_PM_QUOTA}</div>
				</div>
				<div class="module">
					<label>账户状态：</label>
					<div>
						<input type="radio" name="user_status" value="1" {USER_ACTIVE_YES} /> 是
						<input type="radio" name="user_status" value="0" {USER_ACTIVE_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>允许发送信息：</label>
					<div>
						<input type="radio" name="user_allowpm" value="1" {ALLOW_PM_YES} /> 是
						<input type="radio" name="user_allowpm" value="0" {ALLOW_PM_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>允许使用头像：</label>
					<div>
						<input type="radio" name="user_allowavatar" value="1" {ALLOW_AVATAR_YES} /> 是
						<input type="radio" name="user_allowavatar" value="0" {ALLOW_AVATAR_NO} /> 否
					</div>
				</div>
				<div class="module">
					<label>等级：</label>
					<div>
						<select name="user_rank">
							{RANK_SELECT_BOX}
						</select>
					</div>
				</div>
				<div class="module">
					<label>特殊等级标题：</label>
					<div><input type="text" name="user_zvanie" maxlength="50" value="{USER_ZVANIE}" />
				</div>
					<div><input type="checkbox" name="deleteuser" /> 删除用户</div>
					<p>删除后无法恢复</p>
				</div>
				{S_HIDDEN_FIELDS}
				<div class="center">
					<input type="submit" name="submit" value="保存" />
				</div>
			</form>
		</div>