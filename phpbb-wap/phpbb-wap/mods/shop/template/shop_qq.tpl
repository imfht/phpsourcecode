			<div id="main">
				<div class="title">购买网站帐号</div>
				<p>请选择您要的ＱＱ号码：</p>
				<style type="text/css">
					table {width: 100%;}
					td {text-align: center;}
				</style>
				<table border="1">
					<tr>
						<td>ＱＱ</td>
						<td>需要金币</td>
						<td>操作</td>
					</tr>
<!-- BEGIN qq -->
					<tr class="module {qq.ROW_CLASS}">
						<td>{qq.QQ}</td>
						<td>{qq.POINTS}</td>
						<td><a href="{qq.U_BUY}">购买</a></td>
					</tr>
				</div>
<!-- END qq -->
				</table>
<!-- BEGIN not -->
				<div class="module">你来的不是时候，已经被别人抢购完了，等待下一批吧</div>
<!-- END not -->
				{PAGINATION}
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>