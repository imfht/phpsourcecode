			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_ALBUM_AUTH_SELECT}">选择</a>&gt;权限</div>
				<form action="{S_ALBUM_ACTION}" method="post">
					<!-- BEGIN grouprow -->
						<div class="title">{grouprow.GROUP_NAME}</div>
						<div class="row1"><input name="view[]" type="checkbox" {grouprow.VIEW_CHECKED} value="{grouprow.GROUP_ID}" /> 浏览</div>
						<div class="row1"><input name="upload[]" type="checkbox" {grouprow.UPLOAD_CHECKED} value="{grouprow.GROUP_ID}" /> 上传</div>
						<div class="row1"><input name="rate[]" type="checkbox" {grouprow.RATE_CHECKED} value="{grouprow.GROUP_ID}" /> 评价</div>
						<div class="row1"><input name="comment[]" type="checkbox" {grouprow.COMMENT_CHECKED} value="{grouprow.GROUP_ID}" /> 评论</div>
						<div class="row1"><input name="edit[]" type="checkbox" {grouprow.EDIT_CHECKED} value="{grouprow.GROUP_ID}" /> 编辑</div>
						<div class="row1"><input name="delete[]" type="checkbox" {grouprow.DELETE_CHECKED} value="{grouprow.GROUP_ID}" /> 删除</div>
						<div class="row1"><input name="moderator[]" type="checkbox" {grouprow.MODERATOR_CHECKED} value="{grouprow.GROUP_ID}" /> 版主</div>
					<!-- END grouprow -->
					<input name="submit" type="submit" value="提交" />
				</form>
			</div>