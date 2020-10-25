			<div id="main">
				<div class="title">您申请了 {TOTAL_LINKS} 个友链</div>
				<p>点击网站名称可以修改友链信息</p>
				<form id="links" method="post" name="link_list" action="{S_ACTION}">
<!-- BEGIN your_links -->
					<div class="module bm-gary">
						<input type="checkbox" name="id_list[]" value="{your_links.LINK_ID}" />
						{your_links.NUMBER}、<a href="{your_links.U_EDIT_LINK}">{your_links.LINK_TITLE}</a>
					</div>
<!-- END your_links -->		
<!-- BEGIN your_not_link -->
					<div class="module">您没有添加过任何网站</div>
<!-- END your_not_link -->
					<div>
						<input class="button" type="submit" name="submit" value="删除选中" />
						<a class="button" href="#" onclick="marklist('links', 'id_list', true); return false;">选择全部</a>
						<a class="button" href="#" onclick="marklist('links', 'id_list', false); return false;">取消选择</a>				
					</div>
				</form>
				<br />
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>
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