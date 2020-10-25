<?php mc_template_part('header'); ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2" id="group">
				<ul class="nav nav-tabs mt-40 mb-20 post-nav">
					<li role="presentation" class="active">
						<a href="<?php echo U('post/group/index'); ?>">
							社区首页
						</a>
					</li>
					<?php $groups = M('page')->where('type="group"')->order('date desc')->select(); if($groups) : foreach($groups as $val) : ?>
					<li role="presentation">
						<a href="<?php echo U('post/group/single?id='.$val['id']); ?>">
							<?php echo $val['title']; ?>
						</a>
					</li>
					<?php endforeach; endif; ?>
					<li class="pull-right">
						<a href="javascript:;">共有<?php echo M('page')->where("type = 'publish'")->count('id'); ?>个主题</a>
					</li>
				</ul>
				<?php if($page) : ?>
				<div id="post-list-default">
					<ul class="list-group">
					<?php foreach($page as $val) : ?>
					<li class="list-group-item" id="mc-page-<?php echo $val['id']; ?>">
						<div class="row">
							<div class="col-sm-6 col-md-7 col-lg-8">
								<div class="media">
									<?php $author = mc_get_meta($val['id'],'author',true); ?>
									<a class="media-left" href="<?php echo mc_get_url($author); ?>">
										<div class="img-div img-circle">
											<img width="40" class="media-object" src="<?php echo mc_user_avatar($author); ?>" alt="<?php echo mc_user_display_name($author); ?>">
										</div>
									</a>
									<div class="media-body">
										<h4 class="media-heading">
											<a href="<?php echo mc_get_url($val['id']); ?>"><span class="wto"><?php echo $val['title']; ?></span><?php if($val['date']>strtotime("now")) : ?><span class="label label-danger">置顶</span><?php endif; ?></a>
										</h4>
										<p class="post-info wto">
											<i class="glyphicon glyphicon-user"></i><a href="<?php echo mc_get_url($author); ?>"><?php echo mc_user_display_name($author); ?></a>
											<i class="glyphicon glyphicon-home"></i><a href="<?php echo mc_get_url(mc_get_meta($val['id'],'group')); ?>"><?php echo mc_get_page_field(mc_get_meta($val['id'],'group'),'title'); ?></a>
											<i class="glyphicon glyphicon-time"></i><?php echo date('Y-m-d H:i:s',mc_get_meta($val['id'],'time')); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="col-sm-6 col-md-5 col-lg-4 text-right hidden-xs">
								<ul class="list-inline">
								<?php if(mc_last_comment_user($val['id'])) : ?>
								<li>最后：<?php echo mc_user_display_name(mc_last_comment_user($val['id'])); ?></li>
								<?php endif; ?>
								<li>点击：<?php echo mc_views_count($val['id']); ?></li>
								</ul>
							</div>
						</div>
					</li>
					<?php endforeach; ?>
					</ul>
					<?php echo mc_pagenavi($count,$page_now); ?>
				</div>
				<?php else : ?>
				<div id="post-list-default">
					<ul class="list-group">
						<li class="list-group-item text-center" style="padding:120px 0;">
							暂无任何话题！
						</li>
					</ul>
				</div>
				<?php endif; ?>
				<?php if(mc_user_id()) : ?>
				<form role="form" method="post" action="<?php echo U('home/perform/publish'); ?>">
					<div class="row">
						<div class="col-sm-4 col-lg-3">
							<div class="form-group">
								<label>
									版块
								</label>
								<select class="form-control" name="group">
								<?php $group = M('page')->where('type="group"')->order('date asc')->select(); if($group) : foreach($group as $val) : $num++; ?>
									<option value="<?php echo $val['id']; ?>" <?php if($num==1) echo 'selected'; ?>><?php echo $val['title']; ?></option>
								<?php endforeach; endif; ?>
								</select>
							</div>
						</div>
						<div class="col-sm-8 col-lg-9">
							<div class="form-group">
								<label>
									标题
								</label>
								<input name="title" type="text" class="form-control" placeholder="" value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>
							主题内容
						</label>
						<textarea name="content" class="form-control" rows="3"></textarea>
					</div>
					<button type="submit" class="btn btn-warning btn-block">
						<i class="glyphicon glyphicon-ok"></i> 提交
					</button>
				</form>
				<script charset="utf-8" src="<?php echo mc_site_url(); ?>/Kindeditor/kindeditor-all-min.js"></script>
				<script>
					var editor;
					KindEditor.ready(function(K) {
						editor = K.create('textarea[name="content"]', {
							resizeType : 1,
							allowPreviewEmoticons : false,
							allowImageUpload : true,
							height : 300,
							themeType : 'simple',
							langType : 'zh-CN',
							uploadJson : '<?php echo U('Publish/index/upload'); ?>',
							items : ['source', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'clearhtml', 'quickformat', 'selectall', '|', 
					'formatblock', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
					'italic', 'underline', 'strikethrough', 'removeformat', '|', 'image', 'multiimage', 'table', 'hr', 'emoticons', 'baidumap', 'link', 'unlink'],
							afterChange : function() {
								K(this).html(this.count('text'));
							}
						});
					});
				</script>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>