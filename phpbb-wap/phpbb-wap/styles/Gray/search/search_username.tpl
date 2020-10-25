			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;<a href="search.php">搜索</a>&gt;搜索会员</div>
				<div class="title">搜索会员</div>
				<form method="post" name="search" action="search.php?mode=searchuser">
					<div id="box">
						<div>
							<label>用户名</label>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<input class="input" type="text" name="search_username" value="{USERNAME}" />
							<p>您可以使用通用匹配符 * 进行匹配</p>
						</div>
						<div class="center">
							<br />
							<input class="button" type="submit" name="search" value="搜索" />
							<br />&nbsp;
						</div>
					</div>
					<!-- BEGIN switch_select_name -->
					<div id="box">
						<div>
							<label>结果</label>
							<select name="username_list">
								{S_USERNAME_OPTIONS}
							</select>
							<input class="button" type="submit" name="use" value="选中" />
						</div>
					</div>
					<!-- END switch_select_name -->
				</form>
			</div>