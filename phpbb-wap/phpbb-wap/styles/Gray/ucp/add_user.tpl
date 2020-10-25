			<div id="main">
				<div class="nav">
<!-- BEGIN switch_edit_profile -->
					<a href="{U_INDEX}">首页</a>&gt;<a href="{U_VIEWPROFILE}">我的地盘</a>&gt;修改密码
<!-- END switch_edit_profile -->
					
<!-- BEGIN else_edit_profile -->
					<a href="{U_INDEX}">首页</a>&gt;<a href="ucp.php?mode=register">注册协议</a>&gt;填写注册信息
<!-- END else_edit_profile -->
				
				</div>
				<form action="{U_UCP}" method="post">
					<div class="title">
					
<!-- BEGIN switch_edit_profile -->
						修改密码
<!-- END switch_edit_profile -->		
						
<!-- BEGIN else_edit_profile -->
						填写注册信息
<!-- END else_edit_profile -->
						
					</div>
					{ERROR_BOX}
<!-- BEGIN switch_namechange_disallowed -->
					<div class="module bm-gray">
						<div>
							<label>当前用户：</label>
							<input type="hidden" name="{VAR_USERNAME}" value="{USERNAME}" />
							<strong>{USERNAME}</strong>
						</div>
					</div>
<!-- END switch_namechange_disallowed -->
				
<!-- BEGIN switch_namechange_allowed -->
					<div class="module bm-gray">
						<label>用户名：{L_WARNING}</label>
						<div><input type="text" name="{VAR_USERNAME}" value="{USERNAME}" /></div>
					</div>
<!-- END switch_namechange_allowed -->
					<div class="module bm-gray">
						<label>电子邮件地址：{L_WARNING}</label>
						<div><input type="text" name="{VAR_EMAIL}" value="{EMAIL}" /></div>
					</div>
					<div class="module bm-gray">
						<label>性别：{L_WARNING}</label>
						<div><input type="radio" name="gender" value="1" {GENDER_MALE_CHECKED}/> 男</div>
						<div><input type="radio" name="gender" value="2" {GENDER_FEMALE_CHECKED}/> 女</div>
						<div><input type="radio" name="gender" value="0" {GENDER_NO_SPECIFY_CHECKED}/> 保密</div>
					</div>
<!-- BEGIN switch_edit_profile -->
					<div class="module bm-gray">
						<label>当前密码：{L_WARNING}</label>
						<div><input type="password" name="{VAR_CUR_PASSWORD}" value="{CUR_PASSWORD}" /></div>
					</div>
<!-- END switch_edit_profile -->
					<div class="module bm-gray">
						<label>新密码：{L_WARNING}</label>
						<div><input type="password" name="{VAR_NEW_PASSWORD}" value="{NEW_PASSWORD}" /></div>
					</div>
					<div class="module">
						<label>密码确认：{L_WARNING}</label>
						<div><input type="password" name="{VAR_PASSWORD_CONFIRM}" value="{PASSWORD_CONFIRM}" /></div>
					</div>
<!-- BEGIN switch_confirm -->
					<div class="module bm-gray">
						<div>{CONFIRM_IMG}</div>
						<label>请输入上图中显示的数字：</label>
						<div><input type="text" name="{VAR_CONFIRM_CODE}" value="" /></div>
					</div>
<!-- END switch_confirm -->
					<div class="center">
						<br />
						{S_HIDDEN_FIELDS}
<!-- BEGIN switch_edit_profile -->
						<input class="button" type="submit" name="submit" value="保存修改" />
<!-- END switch_edit_profile -->
<!-- BEGIN else_edit_profile -->
						<input class="button" type="submit" name="submit" value="完成注册" />
<!-- END else_edit_profile -->
						<br />&nbsp;
					</div>
				</form>
			</div>