			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_PROFILE}">用户中心</a>&gt;相似会员</div>
				<p>与 <b>{USERNAME}</b> 相同信息的会员！</p>
				<div class="title">密码相同</div>
<!-- BEGIN clone_password -->
				<div class="module {clone_password.ROW_CLASS}">	
					<a href="{clone_password.U_LINK_PASSWORD}">{clone_password.LINK_PASSWORD}</a>
				</div>
<!-- END clone_password -->	
<!-- BEGIN not_clone_password -->
				<div class="module">没有密码相同的会员</div>
<!-- END not_clone_password -->
				<div class="title">ＱＱ相同</div>
<!-- BEGIN clone_qq -->
				<div class="module {clone_qq.ROW_CLASS}">	
					<a href="{clone_qq.U_LINK_QQ}">{clone_qq.LINK_QQ}</a>
				</div>
<!-- END clone_qq -->	
<!-- BEGIN not_clone_qq -->
				<div class="module">没有ＱＱ相同的会员</div>
<!-- END not_clone_qq -->
<!-- BEGIN not_write_qq -->
				<div class="module">该用户没有填写ＱＱ信息</div>
<!-- END not_write_qq -->
				<div class="title">博客相同</div>
<!-- BEGIN clone_website -->
				<div class="module {clone_website.ROW_CLASS}">	
					<a href="{clone_website.U_LINK_WEBSITE}">{clone_website.LINK_WEBSITE}</a>
				</div>
<!-- END clone_website -->	
<!-- BEGIN not_clone_website -->
				<div class="module">没有相同的会员</div>
<!-- END not_clone_website -->
<!-- BEGIN not_write_website -->
				<div class="module">该用户没有填写博客信息</div>
<!-- END not_write_website -->				
			</div>