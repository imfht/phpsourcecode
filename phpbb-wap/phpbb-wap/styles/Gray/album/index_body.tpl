			<div id="main">
				<div class="title">相册</div>
<!-- BEGIN catrow -->
				<div class="module {catrow.ROW_CLASS}">
					<a href="{catrow.U_VIEW_CAT}">{catrow.CAT_TITLE}</a>[{catrow.PICS}]<br/>
					<p>描述：{catrow.CAT_DESC}</p>
				</div>
<!-- END catrow -->
<!-- BEGIN no_cats -->
				<div class="module">还没有设置分类</div>
<!-- END no_cats -->
				<div class="title">功能</div>
				<div class="module row1"><a href="{U_USERS_PERSONAL_GALLERIES}">用户相册</a></div>
				<div class="module row2"><a href="{U_YOUR_PERSONAL_GALLERY}">我的相册</a></div>
			</div>