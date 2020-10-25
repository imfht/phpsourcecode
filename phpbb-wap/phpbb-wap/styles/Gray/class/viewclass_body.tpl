			<div id="main">
				<div class="title">{FORUM_NAME} 的专题帖子</div>
<!-- BEGIN topicrow -->
				<div class="{topicrow.ROW_CLASS} module">
					{topicrow.NOMER_POSTA}、{topicrow.TOPIC_FOLDER}{topicrow.TOPIC_ATTACHMENT_IMG}{topicrow.TOPIC_TYPE}
					<a href="{topicrow.U_VIEW_TOPIC}">{topicrow.TOPIC_TITLE}</a>
					[阅:{topicrow.VIEWS}/回:{topicrow.REPLIES}/最后回复:{topicrow.LAST_POST_AUTHOR}]{topicrow.S_LAST_POST}
				</div>
<!-- END topicrow -->
<!-- BEGIN switch_no_topics -->
				<div class="module">该专题中没有任何帖子</div>
<!-- END switch_no_topics -->
				<div class="title">分页信息</div>
				{PAGINATION}
				<div>【<a href="{U_CLASS}">返回上级</a>】</div>
				{PAGE_JUMP}				
			</div>