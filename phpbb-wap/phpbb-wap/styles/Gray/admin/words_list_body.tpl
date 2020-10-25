			<div id="main">	
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级面板导航</a>&gt;敏感词汇</div>
				<p>在这里您可以对一些粗俗、骂人、翻动的词汇进行屏蔽替换，例如：草、你妈、共产党等词汇替换为 ** ，使用户不能直接查看到这些字符</p>
				<form method="post" action="{S_WORDS_ACTION}">
					<div class="title">敏感词汇列表</div>
<!-- BEGIN words -->
					<div class="{words.ROW_CLASS} module">
						<div id="xx-word">敏感词汇：{words.WORD}</div>
						<div id="xx-word-replace">替代词汇：{words.REPLACEMENT}</div>
						<div id="word-edit"><a href="{words.U_WORD_EDIT}">修改</a> | <a href="{words.U_WORD_DELETE}">删除</a></div>
					</div>
<!-- END words -->
<!-- BEGIN empty_words -->
					<div class="module">您还没有添加任何敏感词汇</div>
<!-- END empty_words -->
					{S_HIDDEN_FIELDS}
					<div class="center">
						<br />
						<input class="button" type="submit" name="add" value=" 新增 " />
					</div>
					<br />
				</form>
			</div>
				