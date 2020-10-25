			<div id="main">
				<div class="title">{FORUM_NAME} 论坛的版主列表</div>
<!-- BEGIN moderators -->
				<div class="module {moderators.ROW_CLASS}">
					<a href="{moderators.U_UCP}"><span style="{moderators.USERNIC_COLOR}">{moderators.USERNAME}</span></a>
					{moderators.IMG_MOD}
					<hr />
					{moderators.SIGNTURE}
				</div>
<!-- END moderators -->
<!-- BEGIN not_moderator -->
				<div class="module">论坛版主正在招募中</div>
<!-- END not_moderator -->
				<div class="title">版主功能</div>
				<div class="module">
					【<a href="{U_MANAGE_MODULE}">论坛装修</a> . <a href="{U_FORUMCLASS}">专题管理</a>】
				</div>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>