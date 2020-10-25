			<div id="main">
				<h1 class="title">{ARTICLE_TITLE}</h1>
				<div class="module">
					<div class="row1">文章作者：{ARTICLE_POSTER}</div>
					<div class="row2">发表时间：{ARTICLE_TIME}</div>
					<div class="row1">浏览次数：{ARTICLE_VIEWS}</div>
<!-- BEGIN is_poster -->
					<div class="row2"><a href="{is_poster.U_EDIT}">修改/续写</a> . <a href="{is_poster.U_DELETE}">删除</a></div>
<!-- END is_poster -->
				</div>
				<div class="article"><p>{ARTICLE_TEXT}</p></div>
				<div class="module row1">上一篇：{ARTICLE_PREVIOU}</div>
				<div class="module row2">下一篇：{ARTICLE_NEXT}</div>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>
			<script src="./styles/Gray/js/jquery.min.js"></script>
			<script src="./styles/Gray/js/jQuery.imgAutoSize.js"></script>
			<script type="text/javascript">
			jQuery(function ($) {
				$('.articleImg').imgAutoSize();
			});
			</script>