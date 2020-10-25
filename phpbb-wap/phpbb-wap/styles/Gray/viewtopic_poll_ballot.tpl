				<form method="POST" action="{S_POLL_ACTION}">
					<div class="title">{POLL_QUESTION}</div>
<!-- BEGIN poll_option -->
					<div class="module bm-gray">
						<input type="radio" name="vote_id" value="{poll_option.POLL_OPTION_ID}"/>
						{poll_option.POLL_OPTION_CAPTION}
					</div>
<!-- END poll_option -->
					<input type="submit" name="submit" value="参与投票"/>
					 . <a href="{U_VIEW_RESULTS}">查看投票结果</a>
					{S_HIDDEN_FIELDS}
				</form>