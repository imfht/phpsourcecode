			<div id="main">
				<div class="title">图片评论</div>
					<div class="module">图片上传者：{POSTER}</div>
<!-- BEGIN switch_comment -->
					<div class="title">评论列表</div>
<!-- END switch_comment -->
<!-- BEGIN commentrow -->
					<div class="module {commentrow.ROW_CLASS}">
						<b>{commentrow.POSTER}说：{commentrow.TEXT}[{commentrow.TIME}]</b>--- {commentrow.EDIT} / {commentrow.DELETE}<br />
						
					</div>
<!-- END commentrow -->
<!-- BEGIN switch_not_comment -->
					<div class="module">没有任何评论</div>
<!-- END switch_not_comment -->
					{PAGINATION}
<!-- BEGIN switch_comment_post -->
					<form name="commentform" action="{S_ALBUM_ACTION}" method="post">
						<div class="title">评论</div>
	<!-- BEGIN logout -->
						<div class="module">
							用户名：<input type="text" name="comment_username" maxlength="32" />
						</div>
	<!-- END logout -->
						<div class="module">
							评论内容：<br />
							<textarea name="comment" rows="5">{S_MESSAGE}</textarea>
						</div>
						<input type="submit" name="submit" value="提交" />
					</form>
<!-- END switch_comment_post -->