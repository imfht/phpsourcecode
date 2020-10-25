			<div class="main">
				<div class="title">收藏的帖子({ALL_TOPIC_COLLECT})</div>
<!-- BEGIN topic_collect -->				
				<div class="module {topic_collect.ROW_CLASS}">
					{topic_collect.NUMBER}、<a href="{topic_collect.U_TOPIC}">{topic_collect.TITLE}</a>[<a href="{topic_collect.U_DELETE}">移除</a>]
				</div>
<!-- END topic_collect -->
<!-- BEGIN not_topic_collect -->
				<div class="module">您还没有收藏任何帖子</div>
<!-- END not_topic_collect -->
				{PAGINATION}
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>