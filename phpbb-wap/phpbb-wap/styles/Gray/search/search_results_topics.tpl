			<div id="main">
<!-- BEGIN from_ucp -->
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_UCP}">用户中心</a>&gt;主题贴</div>
<!-- END from_ucp -->
<!-- BEGIN from_search -->
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_SEARCH}">搜索</a>&gt;主题贴</div>
<!-- END from_search -->				
				<p>{L_SEARCH_MATCHES}</p>
				<div class="title">主题贴搜索结果</div>
<!-- BEGIN searchresults -->
				<div class="{searchresults.ROW_CLASS} module">
					{searchresults.L_NUMBER}、<a href="{searchresults.U_VIEW_FORUM}">{searchresults.FORUM_NAME}</a> &gt; <a href="{searchresults.U_VIEW_TOPIC}">{searchresults.TOPIC_TITLE}</a> {searchresults.LAST_POST} <br />
					[作者:{searchresults.TOPIC_AUTHOR}/发表时间:{searchresults.FIRST_POST_TIME}]
				</div>
<!-- END searchresults -->
				{PAGINATION}
			</div>