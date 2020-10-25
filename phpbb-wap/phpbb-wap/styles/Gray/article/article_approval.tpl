			<div id="main">
				<div class="title">审核文章</div>
				<form action="{S_ACTION}" id="approval" method="post">
<!-- BEGIN approval -->
					<div class="module {approval.ROW_CLASS}">
						<input type="checkbox" name="id_list[]" value="{approval.ARTICLE_ID}" />
						<a href="{approval.U_ARTICLE}">{approval.ARTICLE_TITLE}</a>
					</div>
<!-- END approval -->
<!-- BEGIN not_approval -->
					<div class="module">没有等待审核的文章</div>
<!-- END not_approval -->
					<div>
						<input class="button" type="submit" name="submit" value="通过审核" />
						<a class="button" href="#" onclick="marklist('approval', 'id_list', true); return false;">选择全部</a>
						<a class="button" href="#" onclick="marklist('approval', 'id_list', false); return false;">取消选择</a>				
					</div>
				</form>
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