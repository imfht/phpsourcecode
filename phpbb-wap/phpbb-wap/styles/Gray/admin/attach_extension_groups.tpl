			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;扩展名小组</div>
				{GROUP_PERMISSIONS_BOX}
				{PERM_ERROR_BOX}
				{ERROR_BOX}
				<p>在这里你可以加入，删除和修改你的扩展名群组，你可以停用扩展名群组，指定特殊类别给它们，变更下载办法而且你可以定义上传图示当做被显示在附件适用到群组的最前面时候。</p>
				<form method="post" action="{S_ATTACH_ACTION}">
					<div class="title">添加一个新的扩展名组</div>
					<div class="module">
						<label>名称：</label>
						<div><input type="text" size="20" name="add_extension_group" value="{ADD_GROUP_NAME}" /></div>
					</div>
					<div class="module"><label>特殊分类：</label>{S_SELECT_CAT}</div>
					<div class="module"><input type="checkbox" name="add_allowed" /> 允许使用</div>
					<div class="module"><label>下载模式：</label>{S_ADD_DOWNLOAD_MODE}</div>
					<div class="module">
						<label>文件上传后显示的图标</label>
						<div><input type="text" name="add_upload_icon" value="{UPLOAD_ICON}" /></div>
					</div>
					<div class="module">文件最大允许 <input type="text" size="3" name="add_max_filesize" value="{MAX_FILESIZE}" /> {S_FILESIZE}</div>
					<div class="module"><input type="checkbox" name="add_extension_group_check" /> 确认添加</div>
					<div class="center">
						<input type="submit" name="submit" value="提交" />
					</div>
<!-- BEGIN grouprow -->
					<div class="title">{grouprow.EXTENSION_GROUP}</div>
					<div class="{grouprow.ROW_CLASS}">
						<input type="text" size="20" name="extension_group_list[]" value="{grouprow.EXTENSION_GROUP}" />
						<strong><a href="{grouprow.U_VIEWGROUP}">{grouprow.CAT_BOX}</a></strong>
						<input type="hidden" name="group_change_list[]" value="{grouprow.GROUP_ID}" />
						<div></div>
	<!-- BEGIN extensionrow -->
						<strong>{grouprow.extensionrow.EXTENSION} {grouprow.extensionrow.EXPLANATION}</strong>
	<!-- END extensionrow -->
						<div class="module"><label>特殊分类：</label>{grouprow.S_SELECT_CAT}</div>
						<div class="module">
							<input type="checkbox" name="allowed_list[]" value="{grouprow.GROUP_ID}" {grouprow.S_ALLOW_SELECTED} />
							允许使用
						</div>
						<div class="module"><label>下载模式：</label>{grouprow.S_DOWNLOAD_MODE}</div>
					<div class="module">
						<label>文件上传后显示的图标</label>
						<div><input type="text" size="15" name="upload_icon_list[]" value="{grouprow.UPLOAD_ICON}" /></div>
					</div>
					<div class="module">
						文件最大允许 <input type="text" size="3" name="max_filesize_list[]" value="{grouprow.MAX_FILESIZE}" />
						{grouprow.S_FILESIZE}
					</div>
					<div class="module"><label>允许论坛：</label><a href="{grouprow.U_FORUM_PERMISSIONS}">权限</a></div>
					<div class="module"><input type="checkbox" name="group_id_list[]" value="{grouprow.GROUP_ID}" /> 删除</div>
<!-- END grouprow -->
					<input type="submit" name="submit" value="保存修改" />
				</form>
			</div>