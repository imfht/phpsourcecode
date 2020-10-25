			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;附件限制</div>
				{ERROR_BOX}
				<p>在这里您可以配置附件的限制</p>
				<form method="post" action="{S_ATTACH_ACTION}">
					<div class="title">新增</div>
					<div class="module bm-gary">
						<label>名称：</label>
						<input type="text" maxlength="25" name="quota_description" value="" />
					</div>
					<div class="module bm-gary">
						<label>限制：</label>
						<input type="text" maxlength="15" size="6" name="add_max_filesize" value="{MAX_FILESIZE}" /> {S_FILESIZE}
					</div>
					{S_HIDDEN_FIELDS}
					<div id="add-quota-check"><input type="checkbox" name="add_quota_check" /> 新增确认</div>
					<div class="">
						<input type="submit" name="submit" value="新增" />
					</div>
					<div class="title">已增列表</div>
<!-- BEGIN limit_row -->
					<div class="{limit_row.ROW_CLASS} module">
						<input type="hidden" name="quota_change_list[]" value="{limit_row.QUOTA_ID}" />
						<div id="quota-name-{limit_row.QUOTA_ID}">
							<label>名称：</label>
							<input type="text" name="quota_desc_list[]" value="{limit_row.QUOTA_NAME}" />
						</div>
						<div id="max=filesize-list-{limit_row.QUOTA_ID}">
							<label>限制：</label>
							<input type="text" size="6" maxlength="15" name="max_filesize_list[]" value="{limit_row.MAX_FILESIZE}" /> {limit_row.S_FILESIZE}
						</div>
						<div><input type="checkbox" name="quota_id_list[]" value="{limit_row.QUOTA_ID}" /> 删除</div>
					</div>
<!-- END limit_row -->
					<div class="center">
						<input type="submit" name="submit" value="保存修改" />
					</div>
				</form>
			</div>