			<div id="main">
				<div class="title">绑定帐号</div>
				{ERROR_BOX}
				<form action="{S_BIND_ACTION}" method="post">
					<div class="module">
						<label>帐号：</label>
						<p>请输入您在本站的用户名或者注册的邮箱！</p>
						<input type="text" name="username" value="" />
					</div>
					<div class="module">
						<label>密码：</label>
						<p>请输入您在本站的密码！</p>
						<input type="password" name="password" value="" />
					</div>
					<div><input type="submit" name="submit" value="绑定" /></div>
				</form>
			</div>