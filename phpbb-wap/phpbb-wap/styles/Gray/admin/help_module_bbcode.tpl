			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_HELP}">使用说明</a>&gt;Module BBCode</div>
				<div class="title">系统内置的MBB</div>
				<div class="module">
					<p><input type="text" value="[br]"> => 换行</p>
					<hr />
					<p><input type="text" value="[n]"> => 空格</p>
					<hr />
					<p><input type="text" value="[LOGO]"> => 显示您当前设置的Logo地址</p>
					<hr />
					<p><input type="text" value="[当前时间]"> => 显示当前时间，例如：8:00</p>
					<hr />
					<p><input type="text" value="[年]"> => 显示年份，例如：2014</p>
					<hr />
					<p><input type="text" value="[月]"> => 显示月份，例如：3</p>
					<hr />
					<p><input type="text" value="[日]"> => 显示日，例如：3</p>
					<hr />
					<p><input type="text" value="[时]"> => 显示小时，例如：8</p>
					<hr />
					<p><input type="text" value="[分]"> => 显示分钟，例如：3</p>
					<hr />
					<p><input type="text" value="[秒]"> => 显示秒，例如：28</p>
					<hr />
					<p><input type="text" value="[星期]"> => 显示星期几，例如：三</p>
					<hr />
					<p><input type="text" value="[问候语]"> => 显示问候语，例如：早上</p>
					<hr />
					<p><input type="text" value="[备案信息]"> => 显示您设置的备案信息</p>
					<hr />
					<p><input type="text" value="[用户名]"> => 显示当前访问的用户名，例如：admin</p>
					<hr />
					<p><input type="text" value="[用户ID]"> => 显示当前访问的用户ID，例如：1</p>
					<hr />
					<p><input type="text" value="[未读信息]"> => 显示用户的未读信息数，例如：3</p>
					<hr />
					<p><input type="text" value="[新信息]"> => 显示用户的信息数，例如：3</p>
					<hr />
					<p><input type="text" value="[金币]"> => 显示用户的金币数量，例如：10000</p>
					<hr />
					<p><input type="text" value="[注册时间]"> => 显示用户的注册时间，例如：2014-06-01 12:30</p>
					<hr />
					<p><input type="text" value="[上级页面]"> => 获取上级页面地址</p>
					<hr />
					<p><input type="text" value="[网页标题]"> => 获取当前网页的标题</p>
					<hr />
					<p><input type="text" value="[邮箱]"> => 显示用户的邮箱地址，例如：mail@phpbb-wap.com</p>
					<hr />
					<p><input type="text" value="[新会员]"> => 获得新会员的用户名（注意：需要开启该功能请打开includes/class/module_bbcode.php按说明修改）</p>
					<hr />
					<p><input type="text" value="[新会员]"> => 获得新会员的用户ID（注意：需要开启该功能请打开includes/class/module_bbcode.php按说明修改）</p>
					<hr />
					<p><input type="text" value="[统计会员]"> => 统计您网站会员（注意：需要开启该功能请打开includes/class/module_bbcode.php按说明修改）</p>
					<hr />
					<p><input type="text" value="[统计主题]"> => 统计您网站的主题（注意：需要开启该功能请打开includes/class/module_bbcode.php按说明修改）</p>
					<hr />
					<p><input type="text" value="[统计帖子]"> => 统计您网站的帖子（注意：需要开启该功能请打开includes/class/module_bbcode.php按说明修改）</p>
					<hr />
					<p><input type="text" value="[统计附件]"> => 统计您网站的附件（注意：需要开启该功能请打开includes/class/module_bbcode.php按说明修改）</p>
					<hr />		
					<p><input type="text" value="[html]HTML代码[/html]"> => 使用HTML代码</p>
					<hr />
					<p><input type="text" value="[MOD=MOD路径]"> => 生成MOD的链接</p>
					<hr />
					<p><input type="text" value="[我的地盘_N]"> => 其中N为-1时指向首页，N为0时取得访问用户的ID，N为用户ID时执行指定的用户地盘，其实这个MBB可以这样结合用[我的地盘_[用户ID]]</p>
					<hr />
					<p><input type="text" value="[调用帖子_A_B_C_D_E]"> => </p>
					<p>A：【论坛ＩＤ】调用的论坛ID：如果调用多个论坛用英文逗号分隔，0表示所有</p>
					<p>B：【使用换行】调用每个帖子链接后面添加一个换行符&lt;br&nbsp;/&gt;：0、不添加、1、表示添加</p>
					<p>C：【显示方式】帖子显示的格式：1、显示帖子的名称，2、显示带帖子名称的超链接，3、显示带标题号的帖子名称，4、显示带标题号的帖子超链接</p>
					<p>D：【调用类型】调用的帖子类型：1、最新帖子，2、一个星期内的热门帖子，3、随机帖子，4、回复人数最多的帖子，5、动态帖子，6、精华帖子，7、专题帖子</p>
					<p>E：【返回条数】显示多少个帖子：最多可设置50</p>
					<hr />	
					<p><input type="text" value="[调用友链_A_B_C_D]"> => </p>
					<p>A：【分类ＩＤ】友链的分类ID：如果调用多个分类用英文逗号分隔，0表示所有</p>
					<p>B：【使用换行】调用每个友链后面添加一个换行符&lt;br&nbsp;/&gt;：0、不添加、1、表示添加</p>
					<p>C：【排序方式】友情链接的排序方式：1、最新链入的链接在前面，2、链入数量最多的链接在前面，3、最新加入的在前面</p>
					<p>D：【返回条数】显示多少个链接：最多可设置50</p>
					<hr />
					<p><input type="text" value="[调用文章_A_B_C_D_E]"> => </p>
					<p>A：【分类ＩＤ】文章的分类ID：如果调用多个分类用英文逗号分隔，0表示所有</p>
					<p>B：【使用换行】调用每个帖子链接后面添加一个换行符&lt;br&nbsp;/&gt;：0、不添加、1、表示添加</p>
					<p>C：【显示方式】文章显示的格式：1、正常显示，2、文章链接前面添加一个文章分类链接，3、文章链接前面添加一个文章分类</p>
					<p>D：【调用类型】调用的文章类型：1、最新文章，2、本周热门</p>
					<p>E：【返回条数】显示多少篇文章：最多可设置50</p>
					<hr />
					<p><input type="text" value="[调用相册_A_B_C_D]"> => </p>
					<p>A：【返回条数】显示多少张图片：最多可设置50</p>
					<p>B：【像素宽度】每张图片的宽度（单位/像素）</p>
					<p>C：【像素高度】每张图片的宽度（单位/像素）</p>
					<p>D：【标题长度】图片下面的标题长度：其中-1为原标题长度，0为不显示</p>
				</div>
				<div class="title">通过MODS扩展的MBB</div>
				<div class="module">
<!-- BEGIN mods_mbb -->
					<p><input type="text" value="{mods_mbb.TAG}" /> => {mods_mbb.METHOD}</p>
					<hr />
<!-- END mods_mbb -->
<!-- BEGIN empty_mods_mbb -->
					<p>没有扩展的Module BBCode</p>
<!-- END empty_mods_mbb -->					
				</div>
			</div>