<!-- BEGIN admin_module_header -->
			<div class="nav"><div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级管理面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;配置</div></div>
			<p>在这里您可以管理首页的全部模块，下面将是一个完整的首页后台管理方案</p>
			<div class="module bm-gray">【<a href="{U_CREATE}">新建模块</a> . <a href="{U_HEADER}">全局顶部</a> . <a href="{U_FOOTER}">全局底部</a>】</div>
			<div class="module bm-gray">【<a href="{U_LOGO}">修改Logo</a> . <a href="{U_HEAD}">修改head部分</a>】</div>
<!-- END admin_module_header -->
			<div id="main">
				{MODULE_TOP}
<!-- BEGIN switch_user_logged_out -->
				<div class="index-cat"><a href="login.php">登录</a> / <a href="ucp.php?mode=register">注册</a> / <img src="./mods/qqlogin/images/qq_login.png" /><a href="{U_QQ_LOGIN}">QQ登录</a></div>
<!-- END switch_user_logged_out -->
				
<!-- BEGIN switch_user_logged_in -->
				<div class="index-cat"><a href="{U_PEIVATE}">收信箱({UNREAD_PM})</a>|<a href="{U_UCP}">我的地盘</a>|<a href="{U_LOGOUT}">注销登录</a>{DISPLAY_HIDE}</div>
<!-- END switch_user_logged_in -->
<!-- BEGIN module_main -->
				{module_main.MODULE_TEXT}{module_main.U_INSERT_EDIT}{module_main.MODULE_TITLE}{module_main.MODULE_BR}
<!-- END module_main -->
				{MODULE_BOTTOM}
			</div>
<!-- BEGIN admin_module -->
			<div>【<a href="{U_ADMIN_MODULE}">页面编辑模式</a> . <a href="{U_ADMIN}">网站后台管理</a>】</div>
<!-- END admin_module -->
<!-- BEGIN admin_module_nav -->
			<div>【<a href="{admin_module_nav.U_PAGE_AGO}">返回上级</a>】</div>
			<div>【<a href="{admin_module_nav.U_PAGE_INDEX}">页面编辑首页</a>】</div>
<!-- END admin_module_nav -->
<!-- BEGIN exit_admin_module -->
			<div>【<a href="{exit_admin_module.U_EXIT}">页面浏览模式</a>】</div>	
<!-- END exit_admin_module -->