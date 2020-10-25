			<div id="main">
				<div class="title">文章分类</div>
<!-- BEGIN article_class -->
				<ul class="article-class" style="">
					<li style="list-style: none;float: left;width: 18%;line-height: 2em;padding: 5px;">
						<a style="text-align: center;display: block;border: solid 1px #F3F3F3;" href="{article_class.U_ARTICLE_CLASS}">{article_class.ARTICLE_CLASS}</a>
					</li>
				</ul>
<!-- END article_class -->
				<div class="clear"></div>
				<div class="title">本月热门</div>
<!-- BEGIN article_hot -->
				<ul>
					<li style="list-style: none;">
						[{article_hot.ARTICLE_NAME}]
						<a href="{article_hot.U_ARTICLE}">{article_hot.ARTICLE_TITLE}</a>
					</li>
				</ul>
<!-- END article_hot -->
<!-- BEGIN not_hot -->
				<div class="module">本周没有热门文章</div>
<!-- END not_hot -->
				<div class="title">最新文章</div>
<!-- BEGIN article_new -->
				<ul>
					<li style="list-style: none;">
						[{article_new.ARTICLE_NAME}]
						<a href="{article_new.U_ARTICLE}">{article_new.ARTICLE_TITLE}</a>
					</li>
				</ul>
<!-- END article_new -->
<!-- BEGIN not_new -->
				<div class="module">还没有任何文章</div>
<!-- END not_new -->
				<div class="title">
					<div class="left">
						<a href="{U_NEW_ARTICLE}">我来投稿</a>
					</div>
<!-- BEGIN admin -->
					<div class="right" style="margin-right: 20px;">
						<a href="{U_APPROVAL}">审核管理</a>
					</div>
<!-- END admin -->
					<div class="clear"></div>
				</div>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>