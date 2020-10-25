			<div id="main">
				<div class="ucp-nav-title center">
					<div class="left ucp-min-title"><a href="{U_UCP_MAIN}">主页</a></div>
					<div class="left ucp-min-title"><a href="{U_VIEWPROFILE}">个人档</a></div>
					<div class="left ucp-min-title"><a href="{U_GUESTBOOK}">留言</a></div>
					<div class="left ucp-min-title"><a href="{U_ALBUM}">相册</a></div>
					<div class="clear"></div>
				</div>
				<div class="module">
					<form action="{U_GUESTBOOK}" method="post">
						<textarea name="message" rows="5" style="width:99%;"></textarea>
						<input type="checkbox" name="look"> 仅主人可见
						<input type="submit" value="提交留言" />
					</form>
				</div>
				<div class="title">留言列表({GUESTBOOK_TOTAL})</div>
<!-- BEGIN guestbook -->
				<div class="module {guestbook.ROW_CLASS}">
					<a href="{guestbook.U_UCP}">{guestbook.USERNAME}</a>说：{guestbook.MESSAGE} {guestbook.TIME}
				</div>
<!-- END guestbook -->
<!-- BEGIN empty_guestbook -->
				<div class="module">空空如也...</div>
<!-- END empty_guestbook -->
				{PAGINATION}
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>	
			</div>