			<div id="main">
				<div class="title">聊天记录</div>
				<!-- BEGIN history -->
				<div class="{history.ROW_CLASS}">
					<b>{history.FROM}</b>说：{history.TEXT} by {history.DATE}
				</div>
				<!-- END history -->
				{PAGINATION}
				<br />
				<a href="{U_NEW_PM}">回复信息</a>
				&nbsp;&nbsp;
				<a href="{S_HTXT}" class="buttom">导出聊天记录</a>
				<br />
				&nbsp;
				{PAGE_JUMP}
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>	
			</div>