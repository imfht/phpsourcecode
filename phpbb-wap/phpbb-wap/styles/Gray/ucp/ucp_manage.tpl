			<div class="main">
				<div class="ucp-nav-title center">
					<div class="left ucp-min-title"><a href="{U_UCP_MAIN}">主页</a></div>
					<div class="left ucp-min-title"><a href="{U_VIEWPROFILE}">个人档</a></div>
					<div class="left ucp-min-title"><a href="{U_GUESTBOOK}">留言</a></div>
					<div class="left ucp-min-title"><a href="{U_ALBUM}">相册</a></div>
					<div class="clear"></div>
				</div>
				<div class="ucp-manage">
					<div>
						<a href="{U_FRIENDS}">我的好友</a>
					</div>				
					<div class="ucp-manage-thread">
						<a href="{U_EDITPROFILEINFO}">修改个人档</a>
					</div>
					<div class="ucp-manage-thread">
						<a href="{U_MAIN_ADMIN}">空间主页管理</a>
					</div>
<!-- BEGIN links -->
					<div class="ucp-manage-thread">
						<a href="{links.U_MANAGE}">我的友链({links.LINKS_TOTAL}条)</a>
					</div>
<!-- END links -->
					<div class="ucp-manage-thread">
						<a href="{U_EDITPROFILE}">修改密码</a>
					</div>
					<div class="ucp-manage-thread">
						<a href="{U_EDITCONFIG}">修改我的设置</a>
					</div>
					<!-- 这些需要管理员账号才能显示 -->
<!-- BEGIN admin -->
					<div class="ucp-manage-thread">{ADMIN_LINK}</div>
					<div class="ucp-manage-thread">{CLONE_USER}</div>
					<div class="ucp-manage-thread">{EDIT_USER}</div>
					<div class="ucp-manage-thread">{BAN_USER}</div>
<!-- END admin -->
<!-- BEGIN delete -->
					<div class="ucp-manage-thread">{DELETE_USER}</div>
<!-- END delete -->
<!-- BEGIN lock -->
					<div class="ucp-manage-thread">{LINK_LOOK}</div>
<!-- END lock -->
				</div>
				<div class="nav"><a href="{U_INDEX}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>	
			</div>