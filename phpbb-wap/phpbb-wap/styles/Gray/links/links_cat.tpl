			<div id="main">
				<div class="title">
					<form action="{U_ACTION}" method="get">
						<select name="sort">
							<option value="0">动态排行</option>
							<option value="1">链入排行</option>
							<option value="2">正在审核</option>
						</select>
						{S_HIDDEN}
						<input type="submit" value="查看" />
						-
						<a href="{U_JOIN}">申请加入</a>
					</form>
				</div>
<!-- BEGIN cat_links -->
				<div class="module {cat_links.ROW_CLASS}">
					{cat_links.NUMBER}、<a href="{cat_links.U_LINK}">{cat_links.LINK_TITLE}</a>
				</div>
<!-- END cat_links -->
<!-- BEGIN not_links -->
				<div class="module">没有该类型的网站</div>
<!-- END not_links -->
				{PAGINATION}
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>