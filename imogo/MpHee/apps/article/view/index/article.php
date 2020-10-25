<link rel="stylesheet" href="__APPURL__/css/appmsg.css" />
<style>
body {
	padding-bottom: 20px;
}

.pagination {
	margin: 0 70px;
	float: right;
}

#first_col {
	display: inline-block;
	zoom: 1;
	*display: inline;
}

#second_col {
	margin-left: 15px;
	display: inline-block;
	zoom: 1;
	*display: inline;
}

.add-btn {
	height: 90px;
	margin: 0 18px;
	color: #b5b5b5;
	background: transparent
		url('__APPURL__/images/appmsg-icon.png') no-repeat 50%
		-242px;
}

.multi-access {
	background-position: 50% -342px;
}

ul {
	padding: 0;
	margin: 0;
}

li {
	list-style-type: none;
}

.sub-msg-item {
	padding: 12px 14px;
	overflow: hidden;
	zoom: 1;
	border-top: 1px solid #c6c6c6;
}

.thumb {
	float: right;
	font-size: 0;
}

.thumb .default-tip {
	font-size: 16px;
	color: #c0c0c0;
	width: 70px;
	line-height: 70px;
	border: 1px solid #b2b8bd;
}

.thumb img {
	width: 70px;
	height: 70px;
	border: 1px solid #b2b8bd;
}

.sub-msg-item .msg-t {
	margin-left: 0;
	margin-right: 85px;
	margin-top: 0;
	padding-left: 4px;
	padding-top: 12px;
	line-height: 24px;
	max-height: 48px;
	font-size: 14px;
	overflow: hidden;
}
</style>
<title>图文素材管理页面</title>
<script>
	$(function() {
		//绑定删除事件
		$(".del-btn").click(
				function() {
					if (confirm("确定删除本图文?")) {
						var $delTarget = $(this);
						$.post("{url('index/del')}", {rid : $(this).attr("data-rid")},function(data) {
								alert(data);
								window.location.href="{url('index/article')}";
						});
					}
				});

		//绑定编辑事件
		$(".edit-btn").click(function() {
			if ($(this).attr("data-mul") == "true") {
				location.href = "{url('index/article_mul_detail_edit',array(action=>edit))}"+"&id="+$(this).attr("data-rid");
			} else {
				location.href = "{url('index/article_detail_addedit',array(action=>edit))}"+"&id="+$(this).attr("data-rid");
			}
		});
	});
</script>
</head>

<body>
	<div class="container">
		<div class="containerBox">
			<div class="boxHeader">
				<h4>素材管理</h4>
			</div>
			<div class="content">
				<div class="w200 fh3" style="display:inline-block">图文列表(共<span id="total_count">{$articlecount}</span>个)</div>
					<div class="t-pages right mr10 mt5">
							{if $currentpage == 1}
							
							{else}
							<a class="prePage" href="{url('index/article',array(start=>(($currentpage - 2)*$limit),limit=>$limit))}">上一页</a>
						    {/if}
							<?php 
								echo "";
								$nextIndex = 0;
								for ($i = 1; $i <= $totalpage; $i++)
								{
									if ($i == $currentpage)
									{
										echo "<span class='current'>$i</span>";
										$nextIndex = $i * $limit;
									}
									else 
									{
										echo "<a class='pages' href='".url('index/article')."&start=" . (($i - 1) * $limit). "&limit=$limit'>$i</a>";
									}
								}
							?>
							{if $currentpage == $totalpage}
							
							{else}
							<a class="nextPage" href="{url('index/article',array(start=>$nextIndex,limit=>$limit))}">下一页</a>
						    {/if}
							
						</div>
						
				<div class="clr"></div>
				<div class="page-bd">
					<div class="tj msg-list">
						<!-- 偶数内容 -->
						<div id="first_col" class="b-dib vt msg-col">
							<div id="addAppmsg" class="tc add-access">
								<a href="{url('index/article_detail_addedit')}" class="dib vm add-btn">+单图文消息</a>
								<a href="{url('index/article_mul_detail_add')}"	class="dib vm add-btn multi-access">+多图文消息</a>
							</div>
							{if $vo['type']=='2'}multi-msg{/if}
							{loop $evenlist $vo}
							{if $vo['type']=='2'}
							<div class="msg-item-wrapper">
								<div class="msg-item multi-msg">
									<div class="appmsgItem">
										<h4 class="msg-t">
											<a href="{url('mobile/show',array(id=>$vo['id']))}"
												class="i-title" target="_blank">{$vo['tit']}</a>
										</h4>
										<p class="msg-meta">
											<span class="msg-date"><?php echo date('Y-m-d H:i:s',$vo['createtime'])?></span>
										</p>
										<div class="cover">
											<p class="default-tip" style="display: none">封面图片</p>
											<img src="{$vo['pic']}" class="i-img" style="">
										</div>
										<p class="msg-text">{$vo['desc']}</p>
									</div>

									{loop $vo["sublist"] $subvo}
									<div class="rel sub-msg-item appmsgItem">
										<span class="thumb"> <img
											src="{$subvo['pic']}"
											class="i-img" style="">
										</span>
										<h4 class="msg-t">
											<a href="{url('mobile/show',array(id=>$subvo['id']))}" target="_blank" class="i-title">{$subvo['tit']}</a>
										</h4>
										<p class="msg-text">{$subvo['desc']}</p>
									</div>
									{/loop}
								</div>
								<div class="msg-opr">
									<ul class="f0 msg-opr-list">
										<li class="b-dib opr-item"><a data-mul="true"
											class="block tc opr-btn edit-btn" href="javascript:void(0);"
											data-rid="{$vo['id']}"><span
												class="th vm dib opr-icon edit-icon">编辑</span></a></li>
										<li class="b-dib opr-item"><a
											class="block tc opr-btn del-btn" href="javascript:void(0);"
											data-rid="{$vo['id']}"><span
												class="th vm dib opr-icon del-icon">删除</span></a></li>
									</ul>
								</div>
							</div>
							{/if}
							{if $vo['type']=='1'}
							<div class="msg-item-wrapper">
								<div class="msg-item">
									<h4 class="msg-t">
										<a href="{url('mobile/show',array(id=>$vo['id']))}"
											class="i-title" target="_blank">{$vo['tit']}</a>
									</h4>
									<p class="msg-meta">
										<span class="msg-date"><?php echo date('Y-m-d H:i:s',$vo['createtime'])?></span>
									</p>
									<div class="cover">
										<p class="default-tip" style="display: none">封面图片</p>
										<img
											src="{$vo['pic']}"
											class="i-img" style="">
									</div>
									<p class="msg-text">{$vo['desc']}</p>
								</div>
								<div class="msg-opr">
									<ul class="f0 msg-opr-list">
										<li class="b-dib opr-item"><a data-mul="false"
											class="block tc opr-btn edit-btn" href="javascript:void(0)"
											data-rid="{$vo['id']}"><span
												class="th vm dib opr-icon edit-icon">编辑</span></a></li>
										<li class="b-dib opr-item"><a
											class="block tc opr-btn del-btn" href="javascript:void(0);"
											data-rid="{$vo['id']}"><span
												class="th vm dib opr-icon del-icon">删除</span></a></li>
									</ul>
								</div>
							</div>
							{/if}
							{/loop}
						</div>
						
						<!-- 奇数内容 -->
						<div id="second_col" class="b-dib vt msg-col">
							{loop $oddlist $vo}
							{if $vo['type']=='2'}
							<div class="msg-item-wrapper">
								<div class="msg-item multi-msg">
									<div class="appmsgItem">
										<h4 class="msg-t">
											<a href="{url('mobile/show',array(id=>$vo['id']))}"
												class="i-title" target="_blank">{$vo['tit']}</a>
										</h4>
										<p class="msg-meta">
											<span class="msg-date"><?php echo date('Y-m-d H:i:s',$vo['createtime'])?></span>
										</p>
										<div class="cover">
											<p class="default-tip" style="display: none">封面图片</p>
											<img
												src="{$vo['pic']}""
												class="i-img" style="">
										</div>
										<p class="msg-text">{$vo['desc']}</p>
									</div>

									{loop $vo["sublist"] $subvo}
									<div class="rel sub-msg-item appmsgItem">
										<span class="thumb"> <img
											src="{$subvo['pic']}"
											class="i-img" style="">
										</span>
										<h4 class="msg-t">
											<a href="{url('mobile/show',array(id=>$subvo['id']))}" target="_blank" class="i-title">{$subvo['tit']}</a>
										</h4>
										<p class="msg-text">{$subvo['desc']}</p>
									</div>
									{/loop}
								</div>
								<div class="msg-opr">
									<ul class="f0 msg-opr-list">
										<li class="b-dib opr-item"><a data-mul="true"
											class="block tc opr-btn edit-btn" href="javascript:void(0);"
											data-rid="{$vo['id']}"><span
												class="th vm dib opr-icon edit-icon">编辑</span></a></li>
										<li class="b-dib opr-item"><a
											class="block tc opr-btn del-btn" href="javascript:void(0);"
											data-rid="{$vo['id']}"><span
												class="th vm dib opr-icon del-icon">删除</span></a></li>
									</ul>
								</div>
							</div>
							{/if}
							{if $vo['type']=='1'}
							<div class="msg-item-wrapper">
								<div class="msg-item">
									<h4 class="msg-t">
										<a href="{url('mobile/show',array(id=>$vo['id']))}"
											class="i-title" target="_blank">{$vo['tit']}</a>
									</h4>
									<p class="msg-meta">
										<span class="msg-date"><?php echo date('Y-m-d H:i:s',$vo['createtime'])?></span>
									</p>
									<div class="cover">
										<p class="default-tip" style="display: none">封面图片</p>
										<img
											src="{$vo['pic']}"
											class="i-img" style="">
									</div>
									<p class="msg-text">{$vo['desc']}</p>
								</div>

								<div class="msg-opr">
									<ul class="f0 msg-opr-list">
										<li class="b-dib opr-item"><a data-mul="false"
											class="block tc opr-btn edit-btn" href="javascript:void(0);"
											data-rid="{$vo['id']}"><span
												class="th vm dib opr-icon edit-icon">编辑</span></a></li>
										<li class="b-dib opr-item"><a
											class="block tc opr-btn del-btn" href="javascript:void(0);"
											data-rid="{$vo['id']}"><span
												class="th vm dib opr-icon del-icon">删除</span></a></li>
									</ul>
								</div>
							</div>
							{/if}
							{/loop}
						</div>
					</div>
				</div>
			</div>
			<div class="clr" />
		</div>
	</div>
</body>
</html>