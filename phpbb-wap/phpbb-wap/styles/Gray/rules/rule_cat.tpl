			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;<a href="./rules.php">规则</a>&gt;{TITLE}</div>
				<form action="{S_RULES_ACTION}" method="post">
<!-- BEGIN r_edit -->
					<div class="title">编辑规则分类</div>
<!-- END r_edit -->
<!-- BEGIN r_add -->
					<div class="title">新增规则分类</div>
<!-- END r_add -->
					<div id="box">
						<div>
							<label>名称</label>
							&nbsp;&nbsp;
							<input type="text" class="input" name="subject" value="{CAT_NAME}" /> 
						</div>
						<div class="center">
							<br />
							<input class="button" type="submit" value=" 确认 " />
						</div>
					</div>
				</form>
			</div>