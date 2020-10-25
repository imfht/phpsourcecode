			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;<a href="{U_UCP}">我的地盘</a>&gt;修改个人资料</div>
				{ERROR_BOX}
				<form action="{S_PROFILE_ACTION}" method="post">
					<div class="title">修改简历</div>
					<div class="module bm-gray">
						<label>雅虎帐号</label>
						<div><input type="text" name="yim" maxlength="255" value="{YIM}" /></div>
					</div>
					<div class="module bm-gray">
						<label>ＱＱ号码</label>
						<div><input type="text" name="qq" value="{QQ}" /></div>
					</div>
					<div class="module bm-gray">
						<label>电话号码</label>
						<div><input type="text" name="number" value="{NUMBER}" /></div>
					</div>
					<div class="module bm-gray">
						<label>个人博客</label>
						<div><input type="text" name="website" value="{WEBSITE}" /></div>
					</div>
					<div class="module bm-gray">
						<label>所在地</label>
						<div><input type="text" name="location" value="{LOCATION}" /></div>
					</div>
					<div class="module bm-gray">
						<label>职业</label>
						<div><input type="text" name="occupation" value="{OCCUPATION}" /></div>
					</div>
					<div class="module bm-gray">
						<label>兴趣爱好</label>
						<div><input type="text" name="interests" value="{INTERESTS}" /></div>
					</div>
					<div class="module bm-gray">
						<label>AIM</label>
						<div><input type="text" name="aim" maxlength="255" value="{AIM}" /></div>
					</div>
					<div class="module bm-gray">
						<label>MSN</label>
						<div><input type="text" name="msn" maxlength="255" value="{MSN}" /></div>
					</div>
					<div class="module bm-gray">
						<label>个性签名<label/>
						<div><textarea type="text" name="signature" maxlength="255">{SIGNATURE}</textarea></div>
					</div>
<!-- Start add - Birthday MOD -->
					<div class="module bm-gray">
						<label>我的生日 {BIRTHDAY_REQUIRED}</label>
						<div>{S_BIRTHDAY}</div>
					</div>
<!-- End add - Birthday MOD -->
<!-- Start add - Gender MOD -->
					<div class="module">
						<label>我的性别</label>
						<div><input type="radio" name="gender" value="1" {GENDER_MALE_CHECKED}/>男</div>
						<div><input type="radio" name="gender" value="2" {GENDER_FEMALE_CHECKED}/>女</div>
						<div><input type="radio" name="gender" value="0" {GENDER_NO_SPECIFY_CHECKED}/>保密</div>
					</div>
<!-- End add - Gender MOD -->
					{S_HIDDEN_FIELDS}
					<div class="center">
						<br />
						<input class="button" type="submit" name="submit" value="确认修改" />
						<br />
						&nbsp;
					</div>
				</form>