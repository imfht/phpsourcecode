			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_ADMIN_MODS}">MODS管理</a>&gt;QQ登录</div>
				<p>如果您没有以下这些信息请先到 <a href="http://connect.qq.com/">http://connect.qq.com/</a> 申请</p>
				<div class="title">QQ互联信息</div>
				{HEADER_BOX}
				<form action="{S_QQLOGIN_ACTION}" method="post">
					<div class="module">
						<label>appid</label>：
						<div><input type="text" name="appid" value="{APPID}"></div>
					</div>
					<div class="module">
						<label>appkey</label>：
						<div><input type="text" name="appkey" value="{APPKEY}"></div>
					</div>
					<div class="module">
						<label>回调地址</label>：
						<div><input type="text" name="callback" value="{CALLBACK}"></div>
					</div>
					<input type="submit" value="保存">
				</form>		
			</div>