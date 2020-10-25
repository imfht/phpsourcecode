			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;黑名单</div>
				<p>在这个选项中您可以设定用户的黑名单，您可以指定一个用户为黑名单，一个指定范围的 IP 地址或是计算机主机名称，这些方法禁止被封锁的用户进入论坛首页，您也可以指定封锁电子邮件地址来防止注册用户使用不同的帐号重复注册，请注意当您只是封锁一个电子邮件地址时将不会影响到用户在您论坛的登陆或是发表文章，您应该使用前面两种方式其中之一或是两种一起来设置黑名单。</p>
				<form method="post" name="post" action="{S_BANLIST_ACTION}">
					<div class="title">添加会员黑名单</div>
					<div class="row1">
						{S_HIDDEN_FIELDS}
						输入用户名：<input type="text" name="username" maxlength="50"/>
						<input type="hidden" name="mode" value="edit" />
					</div>
					<div class="title">会员黑名单列表</div>
					<div class="module">{S_UNBAN_USERLIST_SELECT}</div>
					<div class="title">添加IP黑名单</div>
					<p>要指定多个不同的 IP 地址或是主机名称，请使用英文逗号（,）来分隔它们，要指定 IP 地址的范围，请使用（-）来分隔起始地址及结束地址，或是使用统配符（*）。注意：当您输入一个IP地址范围时，这个范围内所有的IP地址都将会被封锁，您可以使用统配符 * 定义要封锁的ip地址来降低被攻击的可能，如果您一定要输入一个范围请尽量保持精简和适当以免影响正常的使用。<p>
					<div>请输入IP：<input type="text" name="ban_ip"/></div>
					<div class="title">IP黑名单列表</div>
					<div class="module">{S_UNBAN_IPLIST_SELECT}</div>
					<div class="title">添加邮件黑名单</div>
					<p>要指定多个不同的电子邮件地址，请使用逗号（,）来分隔它们，或是使用通配符（*），例如：*@hotmail.com</p>
					<div>请输入E-mail：<input type="text" name="ban_email"/></div>
					<div class="title">E-mail黑名单列表</div>
					<div class="module">{S_UNBAN_EMAILLIST_SELECT}</div>
					<input type="submit" name="submit" value="保存" />
				</form>
			</div>