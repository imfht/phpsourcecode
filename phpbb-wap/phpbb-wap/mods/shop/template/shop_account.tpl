			<div id="main">
				<div class="title">购买网站帐号</div>
				<p>您可以使用您的金币购买一些超过 x 月未登录的帐号，下面为您精心准备了一些：</p>
				<style type="text/css">
					table {width: 100%;}
					td {text-align: center;}
				</style>
				<table border="1">
					<tr>
						<td>ID</td>
						<td>用户名</td>
						<td>操作</td>
					</tr>
<!-- BEGIN account -->
					<tr class="module {account.ROW_CLASS}">
						<td>{account.ID}</td>
						<td>{account.USERNAME}</td>
						<td><a href="{account.U_BUY}">购买</a></td>
					</tr>
				</div>
<!-- END account -->
				</table>
<!-- BEGIN not -->
				<div class="module">你来的不是时候，已经被别人抢购完了，等待下一批吧</div>
<!-- END not -->
				{PAGINATION}
			</div>