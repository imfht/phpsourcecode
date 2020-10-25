			<div id="main">
				<div class="title">版主管理面板</div>
				<form method="post" action="{S_MODCP_ACTION}">
<!-- BEGIN topicrow -->
					<div class="module {topicrow.ROW_CLASS}">
						<input type="checkbox" name="topic_id_list[]" value="{topicrow.TOPIC_ID}" />
						{topicrow.TOPIC_TYPE}
						<a href="{topicrow.U_VIEW_TOPIC}">{topicrow.TOPIC_TITLE}</a>
						[回:{topicrow.REPLIES}/上次回复:{topicrow.LAST_POST_TIME}]
					</div>
<!-- END topicrow -->
<!-- BEGIN not_topic -->
					<div class="module">没有可以管理的帖子</div>
<!-- END not_topic -->
					<div class="nav">
						选中项：
						<input type="submit" name="delete" value="删除" />
						<input type="submit" name="move" value="移动" />
						<input type="submit" name="lock" value="锁定" />
						<input type="submit" name="unlock" value="解锁" />
						{S_HIDDEN_FIELDS}
					</div>
				</form>
				{PAGINATION}
				<div>【<a href="{U_VIEW_FORUM}">返回上级</a>】</div>
				{PAGE_JUMP}