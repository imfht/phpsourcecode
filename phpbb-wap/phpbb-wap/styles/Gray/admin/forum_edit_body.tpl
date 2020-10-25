			<div id="mian">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;<a href="{U_ADMIN_FORUMS}">论坛列表</a>&gt;{L_TITLE}</div>
				<form action="{S_FORUM_ACTION}" method="post">
					<div class="title">论坛信息</div>
					<div class="module bm-gray">
						<label>名称：</label>
						<div><input type="text" name="forumname" value="{FORUM_NAME}" /></div>
					</div>
					<div class="module bm-gray">
						<label>ICON：</label>
						<div><input type="text" size="25" name="forumicon" value="{F_ICON}" class="post" /></div>
					</div>
					<div class="module bm-gray">
						<label>分类：</label>
						<select name="c">{S_CAT_LIST}</select>
					</div>
					<div class="module bm-gray">
						<label>状态：</label>
						<select name="forumstatus">{S_STATUS_LIST}</select>
					</div>
					<div class="module bm-gray">
						<label>自动任务：</label>
						<p>开启时下面两个选项才会生效</p>
						<div><input type="checkbox" name="prune_enable" value="1" {S_PRUNE_ENABLED} /> 开启</div>
						<div>自动删除 <input type="text" name="prune_days" value="{PRUNE_DAYS}" size="5" /> 天内没有回复的主题</div>
						<div>每 <input type="text" name="prune_freq" value="{PRUNE_FREQ}" size="5" /> 天检查一次</div>
						<input type="checkbox" name="forum_postcount" value="1" {S_FORUM_POSTCOUNT} /> 帖子统计
					</div>
					<div class="module">
						用户发表一个帖子会获得
						<input type="text" name="forum_money" value="{FORUM_MONEY}" size="5" />
						积分
					</div>
					{S_HIDDEN_FIELDS}
					<input type="submit" name="submit" value="{S_SUBMIT_VALUE}" />
				</form>
			</div>