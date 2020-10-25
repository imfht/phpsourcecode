			<div id="main">
				<div class="title">请输入广告信息</div>
				{ERROR_BOX}
				<form action="" method="post">
					<div class="module row1">
						标题：
						<input type="text" name="name" value="" />
					</div>
					<div class="module row2">
						位置：
						<select name="type">
							<option value="0">顶部（{TOP_AD}{POINTS_NAME}/天）</option>
							<option value="1">底部（{FOOT_AD}{POINTS_NAME}/天）</option>
						</select>
					</div>
					<div class="module row1">
						URL：
						<input type="text" name="url" value="http://" />
					</div>
					<div class="module row2">
						显示 <input type="text" name="day" value="" size="3" maxlength="11" /> 天？
						<p>至少需要投放 {MIN_DAY} 天，最多不能超过 {MAX_DAY} 天</p>
					</div>
					<div>
						<input type="submit" name="submit" value="发布" />
					</div>
				</form>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>