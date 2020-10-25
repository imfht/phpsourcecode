			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;<a href="{U_ADMIN}">超级面板</a>&gt;<a href="{U_HELP}">使用说明</a>&gt;<a href="{U_PATCH}">程序管家</a>&gt;{TITLE}</div>
				<div class="title">{TITLE}</div>
				<p style="text-indent:28px;">{DESC}</p>
				<div class="title">修改方案</div>
				<dl class="codebox">
					<dt>代码: <a href="#" onclick="selectCode(this); return false;">全选</a></dt>
					<dd>
						<code>
							<pre>{CODE}</pre>
						</code>
					</dd>
				</dl>
				</div>
			</div>
			<script type="text/javascript">
			function selectCode(a)
			{
				// Get ID of code block
				var e = a.parentNode.parentNode.getElementsByTagName('code')[0];

				// Not IE
				if (window.getSelection)
				{
					var s = window.getSelection();
					// Safari
					if (s.setBaseAndExtent)
					{
						s.setBaseAndExtent(e, 0, e, e.innerText.length - 1);
					}
					// Firefox and Opera
					else
					{
						// workaround for bug # 42885
						if (window.opera && e.innerHTML.substring(e.innerHTML.length - 4) == '<BR>')
						{
							e.innerHTML = e.innerHTML + '&nbsp;';
						}

						var r = document.createRange();
						r.selectNodeContents(e);
						s.removeAllRanges();
						s.addRange(r);
					}
				}
				// Some older browsers
				else if (document.getSelection)
				{
					var s = document.getSelection();
					var r = document.createRange();
					r.selectNodeContents(e);
					s.removeAllRanges();
					s.addRange(r);
				}
				// IE
				else if (document.selection)
				{
					var r = document.body.createTextRange();
					r.moveToElementText(e);
					r.select();
				}
			}
			</script>