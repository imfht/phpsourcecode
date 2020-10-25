			<div id="main">				
				<!-- BEGIN switch_box_size_notice -->
				<div class="title">{BOX_SIZE_STATUS}</div>
				<!-- END switch_box_size_notice -->
				
				<p>
					<br />
					{IMG_POSTPM}<a href="{U_POST_PM}" class="buttom">写信息</a>
					{INBOX_IMG}{INBOX}
					{SENTBOX_IMG}{SENTBOX}
					{OUTBOX_IMG}{OUTBOX}
					{SAVEBOX_IMG}{SAVEBOX}
					<br />
					&nbsp;
				</p>
				
				<!-- BEGIN listrow -->
				<div class="{listrow.ROW_CLASS}">
					{listrow.PRIVMSG_FOLDER_IMG}
					<a href="{listrow.U_READ}">{listrow.SUBJECT}</a><br />
					[来自：{listrow.FROM}/时间：{listrow.DATE}]
				</div>
				<!-- END listrow -->

				<!-- BEGIN switch_no_messages -->
				<p>没有任何信息！</p>
				<!-- END switch_no_messages -->

				{PAGINATION}
				<br />
				<div class="center">
					<form method="post" name="privmsg_list" action="{S_PRIVMSGS_ACTION}">
						<input class="button" type="submit" name="deleteall" value="全部删除" />
					</form>
					<br />
				</div>
				{PAGE_JUMP}
				<div class="nav"><a href="{U_UCP}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>