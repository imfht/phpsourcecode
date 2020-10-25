			<div id="main">
				<div class="title">{CAT_TITLE}</div>
				<div class="module row2"><a href="{U_NEW_ARTICLE}">发表文章</a></div>
<!-- BEGIN article_cat -->
				<div class="module {article_cat.ROW_CLASS}">
					{article_cat.NUMBER}、<a href="{article_cat.U_ARTICLE}">{article_cat.ARTICLE_TITLE}</a>
				</div>
<!-- END article_cat -->
<!-- BEGIN not_article -->
				<div class="module">“{CAT_TITLE}”分类下没有任何文章</div>
<!-- END not_article -->
				{PAGINATION}
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>