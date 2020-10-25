			<div id="main">
				<div class="title">创建帐号</div>
				{ERROR_BOX}
				<form action="{S_CREATE_ACTION}" method="post">
					<div class="module">
						<label>帐号：</label>
						<p>本站默认使用你的QQ昵称作为用户名，但是你可以修改他！</p>
						<input type="text" name="username" value="{USERNAME}" />
					</div>
					<div class="module">
						<label>E-mail：</label>
						<p>请输入电子邮件地址！</p>
						<input type="test" name="user_email" value="" />
					</div>
					<div><input type="submit" name="submit" value="创建" /></div>
				</form>
			</div>