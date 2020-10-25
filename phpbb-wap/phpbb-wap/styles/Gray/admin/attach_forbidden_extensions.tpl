			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;禁止扩展名</div>
				{ERROR_BOX}
				<p>在这里你可以加入或删除禁止的扩展名。这些扩展名 php、php3 和 php4 是内定预设禁止的基于安全理由，建议不要删除它们。</p>
				<form method="post" action="{S_ATTACH_ACTION}">
					<div class="title">添加禁止扩展名</div>
					<div class="module">
						<input type="text" maxlength="15" name="add_extension" value=""/>
					</div>
					<div class="module"><input type="checkbox" name="add_extension_check" /> 确认添加</div>
					{S_HIDDEN_FIELDS}
					<div class="title">已禁止的扩展名</div>
					<p>选中项将会被删除</p>
<!-- BEGIN extensionrow -->
					<div class="{extensionrow.ROW_CLASS} module">
						<input type="checkbox" name="extension_id_list[]" value="{extensionrow.EXTENSION_ID}" /> {extensionrow.EXTENSION_NAME}
					</div>
<!-- END extensionrow -->
					<div class="center">
						<input type="submit" name="submit" value="保存更改" />
					</div>
				</form>
			</div>