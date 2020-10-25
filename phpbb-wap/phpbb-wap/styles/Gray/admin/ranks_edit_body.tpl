			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_ADMIN_RANKS}">等级列表</a>&gt;{L_TITLE}</div>
				<div class="title">{L_TITLE}</div>
				<form action="{S_RANK_ACTION}" method="post">
					<div>
						<div><label>等级名称：</label></div>
						<div><input type="text" name="title" size="35" maxlength="40" value="{RANK}" /></div>
					</div>
					<div>
						<div><label>特殊等级：</label></div>
						<div><input type="radio" name="special_rank" value="1" {SPECIAL_RANK} /> 是</div>
						<div><input type="radio" name="special_rank" value="0" {NOT_SPECIAL_RANK} /> 否</div>
					</div>
					<div>
						<div><label>需要最少发表多少帖子：</label></div>
						<div><input type="text" name="min_posts" size="5" maxlength="10" value="{MINIMUM}" /></div>
					</div>
					<div>
						<div><label>等级的荣誉图标：</label></div>
						<p>填写图标的地址，例如：images/xxx.png</p>
						<div>{IMAGE_DISPLAY}</div>
						<div><input type="text" name="rank_image" size="40" maxlength="255" value="{IMAGE}" /></div>
					</div>
					{S_HIDDEN_FIELDS}	
					<div>
						<input type="submit" name="submit" value="保存" />
					</div>
				</form>
			</div>