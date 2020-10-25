			<div id="main">
				<form action="{S_ACTION}" method="post">
					<div class="title">论坛顶部模块</div>
					<div class="module">
						<textarea name="top" rows="5" style="width:98%;">{MODULE_TOP}</textarea>
					</div>
					<div class="title">论坛底部模块</div>
					<div class="module">
						<textarea name="bottom" rows="5" style="width:98%;">{MODULE_BOTTOM}</textarea>
					</div>
					<div class="module">
						<input type="submit" name="submit" value="保存" />
					</div>
				</form>
				<div class="title">版主模块代码说明</div>
				<p>[br] => 换行</p>
				<p>[n] => 空格</p>
				<p>获取时间的代码有：[当前时间]、[年]、[月]、[日]、[时]、[分]、[秒]、[星期]、[时刻]</p>
				<p>[用户名] => 获取用户的用户名</p>
				<p>[ID] => 获取用户的ID</p>
				<p>[金币] => 获取用户的金币</p>
				<p>[html]HTML代码[/html] => 输出HTML代码</p>
				<p class="red">注意：版主不能使用一些带有恶意、宣传等的代码。</p>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>