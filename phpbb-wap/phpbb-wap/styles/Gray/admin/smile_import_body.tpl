			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_SMILEY_ADMIN}">表情列表</a>&gt;导入</div>
				<p>.pak文件必须存放在 {SMILEY_PATH} 文件夹下才能被识别</p>
				<form method="post" action="{S_SMILEY_ACTION}">
					<div class="title">导入表情</div>
					<div class="module">
						<label>选择备份文件：</label>
						<div>{S_SMILE_SELECT}</div>
					</div>
					<div class="module">
						<input type="checkbox" name="clear_current" value="1" /> 导入前删除旧的表情
					</div>
					<div class="module">
						<label>在出现冲突的情况下：</label>
						<div><input type="radio" name="replace" value="1" checked="checked"/> 把旧的替换掉</div>
						<div><input type="radio" name="replace" value="0" /> 保留旧的</div>
					</div>
					{S_HIDDEN_FIELDS}
					<div class="center">
						<input name="import_pack" type="submit" value="开始导入" />
					</div>
				</form>
			</div>