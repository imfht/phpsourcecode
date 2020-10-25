			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_ALBUM_LISTS}">分类管理</a>&gt;设置</div>
				<form action="{S_ALBUM_ACTION}" method="post">
					<div class="title">创建分类</div>
					<div class="module">
						名称：<br />
						<input name="cat_title" type="text" value="{S_CAT_TITLE}" />
					</div>
					<div class="module">
						描述：<br />
						<textarea name="cat_desc" rows="5">{S_CAT_DESC}</textarea>
					</div>
					<div class="title">权限</div>
					<div class="module row1">
						浏览权限：
						<select name="cat_view_level">
							<option {VIEW_GUEST} value="{S_GUEST}">匿名用户</option>
							<option {VIEW_REG} value="{S_USER}">普通会员</option>
							<option {VIEW_PRIVATE} value="{S_PRIVATE}">私有用户</option>
							<option {VIEW_MOD} value="{S_MOD}">版主</option>
							<option {VIEW_ADMIN} value="{S_ADMIN}">超级管理员</option>
						</select>
					</div>
					<div class="module row2">
						上传权限：
						<select name="cat_upload_level">
							<option {UPLOAD_GUEST} value="{S_GUEST}">匿名用户</option>
							<option {UPLOAD_REG} value="{S_USER}">普通会员</option>
							<option {UPLOAD_PRIVATE} value="{S_PRIVATE}">私有用户</option>
							<option {UPLOAD_MOD} value="{S_MOD}">版主</option>
							<option {UPLOAD_ADMIN} value="{S_ADMIN}">超级管理员</option>
						</select>
					</div>
					<div class="module row1">
						评价权限：
						<select name="cat_rate_level">
							<option {RATE_GUEST} value="{S_GUEST}">匿名用户</option>
							<option {RATE_REG} value="{S_USER}">普通会员</option>
							<option {RATE_PRIVATE} value="{S_PRIVATE}">私有用户</option>
							<option {RATE_MOD} value="{S_MOD}">版主</option>
							<option {RATE_ADMIN} value="{S_ADMIN}">超级管理员</option>
						</select>
					</div>
					<div class="module row2">
						评论权限：
						<select name="cat_comment_level">
							<option {COMMENT_GUEST} value="{S_GUEST}">匿名用户</option>
							<option {COMMENT_REG} value="{S_USER}">普通会员</option>
							<option {COMMENT_PRIVATE} value="{S_PRIVATE}">私有用户</option>
							<option {COMMENT_MOD} value="{S_MOD}">版主</option>
							<option {COMMENT_ADMIN} value="{S_ADMIN}">超级管理员</option>
						</select>
					</div>
					<div class="module row1">
						编辑权限：
						<select name="cat_edit_level">
							<option {EDIT_REG} value="{S_USER}">普通会员</option>
							<option {EDIT_PRIVATE} value="{S_PRIVATE}">私有用户</option>
							<option {EDIT_MOD} value="{S_MOD}">版主</option>
							<option {EDIT_ADMIN} value="{S_ADMIN}">超级管理员</option>
						</select>
					</div>
					<div class="module row2">
						删除权限：
						<select name="cat_delete_level">
							<option {DELETE_REG} value="{S_USER}">普通会员</option>
							<option {DELETE_PRIVATE} value="{S_PRIVATE}">私有用户</option>
							<option {DELETE_MOD} value="{S_MOD}">版主</option>
							<option {DELETE_ADMIN} value="{S_ADMIN}">超级管理员</option>
						</select>
					</div>
					<div class="module row1">
						图片审核：
						<select name="cat_approval">
							<option {APPROVAL_DISABLED} value="{S_USER}">关闭</option>
							<option {APPROVAL_MOD} value="{S_MOD}">版主</option>
							<option {APPROVAL_ADMIN} value="{S_ADMIN}">超级管理员</option>
						</select>
					</div>
					<input type="hidden" value="{S_MODE}" name="mode" />
					<input name="submit" type="submit" value="保存" />
				</form>
			</div>