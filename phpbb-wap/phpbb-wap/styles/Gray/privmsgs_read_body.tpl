			<div id="main">
				<div class="title">查看完整信息</div>
				<div class="print">
					<label id="pm-from">写信人</label>:<a href="{U_FROM_USER_PROFILE}">{MESSAGE_FROM}</a><br />
					<label id="pm-to">收信人</label>:{MESSAGE_TO}<br />
					<label id="pm-date">发送时间</label>:{POST_DATE}<br />
					<label id="pm-subject">信息标题</label>:{POST_SUBJECT}<br />
					<label id="pm-message">信息内容</label>:{MESSAGE}
					<!-- BEGIN postrow -->
					{ATTACHMENTS}
					<!-- END postrow -->
				</div>
				<br />
				<div class="format">
					{REPLY_PM}
					<a href="{S_HISTORY}" class="button">&nbsp;&nbsp;信息记录&nbsp;&nbsp;</a>
				</div>
				<br />
				<form method="post" action="{S_PRIVMSGS_ACTION}">
					{S_HIDDEN_FIELDS}
					<input class="button" type="submit" name="save" value="保存信息"/>
					<input class="button" type="submit" name="delete" value="删除信息"/>
					<!-- BEGIN switch_attachments -->
					<input class="button" type="submit" name="pm_delete_attach" value="删除附件"/>
					<!-- END switch_attachments -->
				</form>
				<br />
				{PAGE_JUMP}
				<div class="nav"><a href="{U_INBOX}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>
			