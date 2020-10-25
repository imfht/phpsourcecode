			<div id="main">
				<div class="nav"><a href="./">首页</a>&gt;搜索</div>
				<div class="print"><a href="search.php?mode=searchuser">搜索会员</a></div>
				<div class="title">搜索匹配选项</div>
				<form action="search.php?mode=results" method="POST">
					<div class="module bm-gray">
						<label>搜索关键词：</label>
						<div><input type="text" name="search_keywords"/></div>
						<p>您可以使用通用匹配符 * 进行匹配，这一项必须填写！</p>
						<div><input type="radio" name="search_terms" value="any" checked="checked" /> 搜索任意帖子</div>
						<div><input type="radio" name="search_terms" value="all" /> 搜索全部帖子</div>
					</div>
					<div class="module bm-gray">
						<label>筛选作者：</label>
						<div><input type="text" name="search_author" /></div>
					</div>
					<div class="module bm-gray">
						<label>筛选论坛分类：</label>
						<div>
							<select name="search_cat">
								{S_CATEGORY_OPTIONS}
							</select>
						</div>
					</div>
					<div class="module bm-gray">
						<label>筛选论坛：</label>
						<div>
							<select name="search_forum">
								{S_FORUM_OPTIONS}
							</select>
						</div>
					</div>
					<div class="module bm-gray">
						<label>筛选时间：</label>
						<div>
							<select name="search_time">
								{S_TIME_OPTIONS}
							</select>
						</div>
					</div>
					<div class="module bm-gray">
						<label>显示文字的长度：</label>
						<div>
							<select name="return_chars">
							{S_CHARACTER_OPTIONS}
							</select>
						</div>
					</div>
					<div class="module bm-gray">
						<label>搜索结果排序：</label>
						<div>
							<select name="sort_by">
								{S_SORT_OPTIONS}
							</select>
						</div>
						<div><input type="radio" name="sort_dir" value="ASC" /> 递增</div>
						<div><input type="radio" name="sort_dir" value="DESC" checked="checked" /> 递减</div>
					</div>
					<div class="module">
						<label>搜索结果的显示方式：</label>
						<div><input type="radio" name="show_results" value="topics" checked="checked" /> 仅显示主题标题</div>
						<div><input type="radio" name="show_results" value="posts" /> 显示标题和帖子</div>
						
					</div>
					<div class="center">
						<br />
						{S_HIDDEN_FIELDS}
						<input class="button" type="submit" value="开始搜索" />
						<br />&nbsp;
					</div>
				</form>
			</div>