<link rel="stylesheet" href="__APPURL__/css/appmsg.css">
<script type="text/javascript" src="__APPURL__/js/articlecom.js"></script>
	<div class="tj msg-list">
						<div id="first_col" class="b-dib vt msg-col">
							{loop $evenlist $vo}
							{if $vo['type']=='2'}
							<div class="msg-item-wrapper">
								<div class="msg-item multi-msg">
									<div class="appmsgItem">
										<h4 class="msg-t">
										   {$vo['tit']}
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
											{$subvo['tit']}
										</h4>
										<p class="msg-text">{$subvo['desc']}</p>
									</div>
									{/loop}
								</div>
							</div>
							{/if}
							{if $vo['type']=='1'}
							<div class="msg-item-wrapper">
								<div class="msg-item">
									<h4 class="msg-t">
										{$vo['tit']}
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
							</div>
							{/if}
							{/loop}
						</div>
	</div>