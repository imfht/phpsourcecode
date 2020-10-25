			<div id="main">
				<div class="title">分类管理</div>
<!-- BEGIN no_pics -->
				<div class="module">这个分类下还没有任何图片</div>
<!-- END no_pics -->
<!-- BEGIN picrow -->
				<div class="module {picrow.ROW_CLASS}">
					图片标题：<a href="{picrow.U_PIC}" {TARGET_BLANK}>{picrow.TITLE}</a><br />
	<!-- BEGIN piccol -->
					<a href="{picrow.piccol.U_PIC}" {TARGET_BLANK}><img src="{picrow.piccol.THUMBNAIL}" border="0" alt="" /></a><br/>{picrow.piccol.APPROVAL}
	<!-- END piccol -->
					上传者：{picrow.POSTER}<br />
					上传时间：{picrow.TIME}<br />
					浏览次数：{picrow.VIEW}<br />
					{picrow.RATING}
					{picrow.COMMENTS}
					{picrow.IP}
					{picrow.EDIT}{picrow.DELETE}{picrow.LOCK}{picrow.MOVE}
				</div>
<!-- END picrow -->
				{PAGINATION}
				版主：{MODERATORS}<br />
				{ALBUM_JUMPBOX}<br />
				<div class="module row1">
					<a href="{U_UPLOAD_PIC}">上传图片</a><br />
					{U_MODERKA}
				</div>