			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;表情列表</div>
				<div class="title">表情列表</div>
				<table border="1" style="width:100%">
					<tr>
						<td>图标</td>
						<td>代码</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
<!-- BEGIN smiles -->
					<tr class="{smiles.ROW_CLASS}">
						<td><img src="{smiles.SMILEY_IMG}" alt="" /> {smiles.EMOT}</td>
						<td>{smiles.CODE}</td>
						<td><a href="{smiles.U_SMILEY_EDIT}">编辑</a></td>
						<td><a href="{smiles.U_SMILEY_DELETE}">删除</a></td>
					</tr>
<!-- END smiles -->
				</table>
				{PAGINATION}
				<form method="post" action="{S_SMILEY_ACTION}">
					{S_HIDDEN_FIELDS}
					<br />
					<div class="center">
						<input class="button" type="submit" name="add" value="新增" />
						<input class="button" type="submit" name="import_pack" value="导入">
						<input class="button" type="submit" name="export_pack" value="导出">
					</div>
					<br />
				</form>
			</div>