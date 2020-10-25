			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_ADMIN_INDEX}">超级管理面板导航</a>&gt;友链</div>
				<div class="title">友链分类 - <a href="{U_CREATE_CAT}">创建</a></div>
<!-- BEGIN linkcat -->
				<div class="module">
					<a href="{linkcat.U_LINKCLASS}">{linkcat.LINKCLASS_NAME}</a>
					<p>{linkcat.LINKCLASS_DESC}</p>
				</div>
<!-- END linkcat -->
<!-- BEGIN not_cat -->
				<div class="module">您目前没有创建任何友链分类</div>
<!-- END not_cat -->
				<div class="title">未审核的链接</div>
				<form id="links" action="{S_ACTION}" method="post">
<!-- BEGIN links_row -->
					<div class="module">
						<input type="checkbox" name="id_list[]" value="{links_row.LINK_ID}" />
						[{links_row.LINK_CAT}] » <a href="{links_row.U_LINKS}">{links_row.LINK_TITLE}</a>
					</div>
<!-- END links_row -->
					<div>
						<input class="button" type="submit" name="delete" value="删除选中" />
						<input class="button" type="submit" name="pass" value="通过审核" />
						<a class="button" href="#" onclick="marklist('links', 'id_list', true); return false;">选择全部</a>
						<a class="button" href="#" onclick="marklist('links', 'id_list', false); return false;">取消选择</a>				
					</div>			
				</form>
<!-- BEGIN not_links -->
				<div class="module">还没有任何人与你友链</div>
<!-- END not_links -->


				
				<div class="title">已审核的链接</div>
				<form id="links_pass" action="{S_ACTION_EDIT}" method="post">
<!-- BEGIN links_pass_row -->
					<div class="module">
						<input type="checkbox" name="id_pass_list[]" value="{links_pass_row.LINK_ID}" />
						[{links_pass_row.LINK_CAT}] » <a href="{links_pass_row.LINK_EDIT}">{links_pass_row.LINK_TITLE}</a>
					</div>
<!-- END links_pass_row -->
					<div>
						<input class="button" type="submit" name="delete" value="删除选中" />
						<input class="button" type="submit" name="pass" value="通过审核" />
						<a class="button" href="#" onclick="marklist('links_pass', 'id_list', true); return false;">选择全部</a>
						<a class="button" href="#" onclick="marklist('links_pass', 'id_list', false); return false;">取消选择</a>				
					</div>			
				</form>
<!-- BEGIN not_links -->
				<div class="module">还没有已审核的链接</div>
<!-- END not_links -->


				{PAGINATION}
			</div>
			<script type="text/javascript">
			/**
			* @参数 id form的id
			* @参数 name 标记的名称
			* @参数 state 选择或反选
			*/
			function marklist(id, name, state)
			{
				var parent = document.getElementById(id);
				
				if (!parent)
				{
					eval('parent = document.' + id);
				}

				if (!parent)
				{
					return;
				}

				var rb = parent.getElementsByTagName('input');
				
				for (var r = 0; r < rb.length; r++)
				{	
					if (rb[r].name.substr(0, name.length) == name)
					{
						rb[r].checked = state;
					}
				}
			}
			</script>