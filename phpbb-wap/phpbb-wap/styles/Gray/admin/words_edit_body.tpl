			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;<a href="{U_WORDS_LISTS}">敏感词列表</a>&gt;{L_WORDS_TITLE}</div>
				<form method="post" action="{S_WORDS_ACTION}">
					<div class="title">{L_WORDS_TITLE}</div>
					<div id="box">
						<div>
							<label>敏感词</label>
							&nbsp;&nbsp;
							<input class="input" type="text" name="word" value="{WORD}" />
						</div>
						<div>
							<label>替换词</label>
							&nbsp;&nbsp;
							<input class="input" type="text" name="replacement" value="{REPLACEMENT}" />
						</div>
						{S_HIDDEN_FIELDS}
						<div class="center">
							<br />
							<input class="button" type="submit" name="save" value=" 提交 " />
						</div>
					</div>
				</form>
			</div>