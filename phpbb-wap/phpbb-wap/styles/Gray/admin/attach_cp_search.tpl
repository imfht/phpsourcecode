			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;附件统计</div>
				<div class="title">您可以请选择</div>
				<form method="post" action="{S_MODE_ACTION}">
					<div class="module">
						{S_VIEW_SELECT} <input type="submit" name="submit" value="执行" />
					</div>
				</form>
				<form method="post" action="{S_SEARCH_ACTION}">
					<div class="title">搜索附件</div>
					<p>如果搜索的选项请留空</p>
					<div class="module bm-gray">
						<label>搜索的论坛：</label>
							<select name="search_forum">
								{S_FORUM_OPTIONS}
							</select>
					</div>
					<div class="module bm-gray">
						<label>排序优先级：</label>
						{S_SORT_OPTIONS}
					</div>
					<div class="module bm-gray">
						<label>显示的方式：</label>
						{S_SORT_ORDER}
					</div>
					<div class="module bm-gray">
						<label>文件名：</label>
						<div><input type="text" name="search_keyword_fname" size="20" /></div>
					</div>
					<div class="module bm-gray">
						<label>描述：</label>
						<div><input type="text" name="search_keyword_comment" size="20" /></div>
					</div>
					<div class="module bm-gray">
						<label>作者：</label>
						<div><input type="text" name="search_author" size="20" /></div>
					</div>
					<div class="module bm-gray">
						<label>附件小于（字节）：</label>
						<div><input type="text" name="search_size_smaller" size="10" /></div>
					</div>
					<div class="module bm-gray">
						<label>附件大于（字节）：</label>
						<div><input type="text" name="search_size_greater" size="10" /></div>
					</div>
					<div class="module bm-gray">
						<label>下载次数小于：</label>
						<div><input type="text" name="search_count_smaller" size="10" /></div>
					</div>
					<div class="module bm-gray">
						<label>下载次数大于：</label>
						<div><input type="text" name="search_count_greater" size="10" /></div>
					</div>
					<div class="module">
						<label>搜索多少天前的附件：</label>
						<div><input type="text" name="search_days_greater" size="10" /></div>
					</div>
					{S_HIDDEN_FIELDS}
					<div class="center">
						<input type="submit" name="search" value="搜索"/>
					</div>
				</form>
			</div>