				<div id="main">
					<div class="title">{USERNAME} 的相册</div>
<!-- BEGIN no_pics -->
					<div class="module">还没有上传任何图片</div>
<!-- END no_pics -->
<!-- BEGIN picrow -->
					<div class="module {picrow.ROW_CLASS}">
						标题：{picrow.TITLE}<br />
	<!-- BEGIN piccol -->
						<a href="{picrow.piccol.U_PIC}" {TARGET_BLANK}><img src="{picrow.piccol.THUMBNAIL}" border="0" alt="{picrow.TITLE}" /></a><br/>
						{picrow.piccol.APPROVAL}
	<!-- END piccol -->
						上传时间：{picrow.TIME}<br />
						浏览次数：{picrow.VIEW}<br />
						用户评价：<a href="{picrow.U_RATING}">{picrow.RATING}</a><br />
						用户评论：<a href="{picrow.U_COMMENTS}">{picrow.COMMENTS}</a><br />
						上传者IP：{picrow.IP}<br />
						{picrow.EDIT}{picrow.DELETE}{picrow.LOCK}
					</div>
<!-- END picrow -->
					{PAGINATION}
					<div class="module"><a href="{U_UPLOAD_PIC}">上传图片</a></div>
				</div>