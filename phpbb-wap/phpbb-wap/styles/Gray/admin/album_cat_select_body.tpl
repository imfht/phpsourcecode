			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;选择</div>
				<form action="{S_ALBUM_ACTION}" method="post">
					<div class="title">选择相册的分类</div>
					<div class="module">
						<select name="cat_id">
							<!-- BEGIN catrow -->
								<option value="{catrow.CAT_ID}">{catrow.CAT_TITLE}</option>
							<!-- END catrow -->
						</select>
						<input name="submit" type="submit" value="选中" />
					</div>
				</form>
			</div>