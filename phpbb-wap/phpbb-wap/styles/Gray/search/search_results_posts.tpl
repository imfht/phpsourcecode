			<div id="main">
<!-- BEGIN from_ucp -->
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_UCP}">用户中心</a>&gt;帖子</div>
<!-- END from_ucp -->
<!-- BEGIN from_search -->
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_SEARCH}">搜索</a>&gt;帖子</div>
<!-- END from_search -->
				<div class="title">帖子搜索结果</div>
<!-- BEGIN searchresults -->
				<div class="{searchresults.ROW_CLASS} module">
					{searchresults.L_NUMBER}、<a href="{searchresults.U_FORUM}">{searchresults.FORUM_NAME}</a>
					&gt;
					<a href="{searchresults.U_POST}">{searchresults.TOPIC_TITLE}</a>
					&gt;
					{searchresults.MESSAGE}
				</div>
<!-- END searchresults -->
				{PAGINATION}
			</div>