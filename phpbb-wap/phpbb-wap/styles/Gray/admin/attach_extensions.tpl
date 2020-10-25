			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;扩展名列表</div>
				{ERROR_BOX}
				<form method="post" action="{S_ATTACH_ACTION}">
					<div class="title">添加新的扩展名</div>
					<div class="module">
						<label>扩展名：</label>
						<input type="text" maxlength="100" name="add_extension" value="{ADD_EXTENSION}" />
					</div>
					<div class="module">
						<label>描述：</label>
						<input type="text" maxlength="100" name="add_extension_explain" value="{ADD_EXTENSION_EXPLAIN}" />
					</div>
					<div class="module">
						<label>指定到小组：</label>
						{S_ADD_GROUP_SELECT}
					</div>
					<div class="module"><input type="checkbox" name="add_extension_check" /> 确认添加</div>
					{S_HIDDEN_FIELDS}
					<input type="submit" name="submit" value="添加" />
					<div class="title">扩展名列表</div>
<!-- BEGIN extension_row -->
					<div class="module bm-gray">
						<div>
							<label>扩展名：</label>
							{extension_row.EXTENSION}
						</div>
						<div>
							<label>描述：</label>
							<input type="text" maxlength="100" name="extension_explain_list[]" value="{extension_row.EXTENSION_EXPLAIN}" />
						</div>
						<div>
							<label>指定的小组：</label>
							{extension_row.S_GROUP_SELECT}
						</div>
						<div>
							<input type="checkbox" name="extension_id_list[]" value="{extension_row.EXT_ID}" />
							删除
						</div>
						<input type="hidden" name="extension_change_list[]" value="{extension_row.EXT_ID}" />
					</div>
<!-- END extension_row -->
					<div class="center"><input type="submit" name="submit" value="保存" /></div>
				</form>
			</div>