			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;<a href="./rules.php">规则</a>&gt;<a href="{U_CAT_RULES}">{CAT_NAME}</a>&gt;{TITLE}</div>
<!-- BEGIN r_add -->
				<div class="title">添加规则</div>
<!-- END r_add -->
<!-- BEGIN r_edit -->
				<div class="title">编辑规则</div>
<!-- END r_edit -->				
				<form action="{S_RULES_ACTION}" method="post">
					<div id="box">
						<div>
							<label>名称：</label>
							<br />
							<input class="input" type="text" name="name" value="{NAME}" />
						</div>
						<div>
							<label>内容：</label>
							<br />
							<textarea class="textarea" name="subject">{TEXT}</textarea>
						</div>
						<div><input type="checkbox" name="moder" value="1"{MODER} /> 标记为（MOD）</div>
						<p>如果您标记该选项，上面两项的内容将不做修改。</p>
						<div class="center">
							<br />
							<input class="button" type="submit" value=" 确认 " />
							<br /><br />
						</div>
					</div>
				</form>
			</div>
			