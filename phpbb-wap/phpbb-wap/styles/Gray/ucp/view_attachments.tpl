			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_UCP}">用户中心</a>&gt;帖子附件</div>
				<div class="title">显示方式</div>
				<form method="post" action="{S_MODE_ACTION}">
					{S_MODE_SELECT}
					{S_ORDER_SELECT}
					<input type="submit" name="submit" value="显示" />
				</form>
				<div class="title">附件列表</div>
<!-- BEGIN attachrow -->
				<div class="{attachrow.ROW_CLASS} module">
					<div>{attachrow.ROW_NUMBER}、{attachrow.VIEW_ATTACHMENT}</div>
					<div>来源：{attachrow.POST_TITLE}</div>
					<div>大小：（{attachrow.SIZE} {attachrow.SIZE_LANG}）</div>
					<div>时间：{attachrow.POST_TIME}</div>
					<div>下载： {attachrow.DOWNLOAD_COUNT}</div>
				</div>
<!-- END attachrow -->
				{PAGINATION}
			</div>