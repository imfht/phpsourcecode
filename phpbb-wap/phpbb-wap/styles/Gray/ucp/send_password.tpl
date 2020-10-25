			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;找回密码</div>
				<form action="{S_UCP_ACTION}" method="post">
					<div class="title">找回密码</div>
					<div id="box">
						<div>
							<label id="username">用户名</label>
							&nbsp;&nbsp;
							<input class="input" type="text" name="username" value="{USERNAME}" />
						</div>
						<br />
						<div>
							<label id="emain">E-mail</label>
							&nbsp;&nbsp;
							<input class="input" type="text" name="email" maxlength="255" value="{EMAIL}" />
						</div>
						<br />
						<div class="center">
							<input class="button" type="submit" name="submit" value="提交"/>
						</div>	
					</div>
				</form>
			</div>