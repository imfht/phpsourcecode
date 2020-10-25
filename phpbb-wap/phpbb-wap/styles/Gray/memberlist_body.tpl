			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;会员列表</div>
				<div class="module"><a href="{U_SEARCH_USER}">搜索会员</a></div>
				<div class="title">排序方式</div>
				<form method="post" action="{S_MODE_ACTION}">
					{S_MODE_SELECT}
					{S_ORDER_SELECT}
					<input type="submit" name="submit" value="排序" />
				</form>
				<div class="title">会员列表</div>
<!-- BEGIN memberrow -->
				<div class="{memberrow.ROW_CLASS} module">
					{memberrow.NUMBER}、<a href="{memberrow.U_VIEWPROFILE}"{memberrow.COLOR}>{memberrow.USERNAME}</a>[{memberrow.L_POSTS}：{memberrow.POSTS}]
				</div>
<!-- END memberrow -->
				{PAGINATION}
			</div>