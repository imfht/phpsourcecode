			<div id="main">
				<form action="{S_FRIEND_ACTION}" method="post">
					<input type="text" name="f" value="" maxlength="8" />
					<input type="submit" value="添加" />
				</form>
<!-- BEGIN friends -->
				<div class="friends clear {friends.ROW_CLASS}">
					<a href="{friends.U_UCP}">
						<div class="friend-avatar left">{friends.AVATAR}</div>
						<div class="friend-name left">{friends.USER}</div>
					</a>
				</div>
<!-- END friends -->
				<div class="clear"></div>
<!-- BEGIN not_friend -->
				<p>还没有添加任何好友</p>
<!-- END not_friend -->
				{PAGINATION}
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>