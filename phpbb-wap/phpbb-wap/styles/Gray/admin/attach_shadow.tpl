			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;幽灵文件</div>
				{ERROR_BOX}
				<p>在这里您可以看到一些在数据库中但不在文件系统中和在文件系统中但不在数据库中的文件，统称 “幽灵文件” </p>
				<form method="post" name="shadow_list" action="{S_ATTACH_ACTION}">
					<div class="title">文件系统</div>
					<p>下列这些文件在文件系统中，但不在数据库中有任何记录的</p>
<!-- BEGIN file_shadow_row -->
					<div class="{file_shadow_row.ROW_CLASS} module">
						<input type="checkbox" name="attach_file_list[]" value="{file_shadow_row.ATTACH_ID}" />
						<a href="{file_shadow_row.U_ATTACHMENT}">{file_shadow_row.ATTACH_FILENAME}</a>
					</div>
<!-- END file_shadow_row -->
					<div class="title">数据库</div>
					<p>下列这些文件在数据库中有记录，但是文件系统中没有此文件的！</p>
<!-- BEGIN table_shadow_row -->
					<div class="{table_shadow_row.ROW_CLASS} module">
						<input type="checkbox" name="attach_id_list[]" value="{table_shadow_row.ATTACH_ID}" />
						{table_shadow_row.ATTACH_FILENAME}
					</div>
<!-- END table_shadow_row -->
					<input type="submit" name="submit" value="删除标记" />
				</form>
			</div>