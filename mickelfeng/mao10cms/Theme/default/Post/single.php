<?php mc_template_part('header'); ?>
	<?php foreach($page as $val) : ?>
	<?php $author = mc_author_id($val['id']); $group_id = mc_get_meta($val['id'],'group'); ?>
	<div class="container post-single">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
				<ul class="nav nav-tabs mt-40 mb-20 post-nav">
					<li role="presentation">
						<a href="<?php echo U('post/group/index'); ?>">
							社区首页
						</a>
					</li>
					<?php $groups = M('page')->where('type="group"')->order('date desc')->select(); if($groups) : foreach($groups as $val_g) : ?>
					<li role="presentation" class="<?php if($group_id==$val_g['id']) echo 'active'; ?>">
						<a href="<?php echo U('post/group/single?id='.$val_g['id']); ?>">
							<?php echo $val_g['title']; ?>
						</a>
					</li>
					<?php endforeach; endif; ?>
					<li class="pull-right">
						<a href="javascript:;">
							<i class="glyphicon glyphicon-time"></i> <?php echo date('m/d H:i',mc_get_meta($val['id'],'time')); ?>
						</a>
					</li>
					<li class="pull-right">
						<a href="javascript:;">
							<i class="glyphicon glyphicon-eye-open"></i> <?php echo mc_views_count($val['id']); ?>
						</a>
					</li>
				</ul>
				<h1 id="single-title" class="mt-0 mb-20"><?php echo $val['title']; ?></h1>
				<div id="single">
					<div id="entry">
						<?php echo mc_magic_out($val['content']); ?>
					</div>
					<hr>
					<div class="bdsharebuttonbox" id="share">
						<a href="#" class="bds_qzone btn btn-default" data-cmd="qzone" title="分享到QQ空间">
							分享到QQ空间
						</a>
						<a href="#" class="bds_tsina btn btn-default" data-cmd="tsina" title="分享到新浪微博">
							分享到新浪微博
						</a>
						<a href="#" class="bds_weixin btn btn-default" data-cmd="weixin" title="分享到微信">
							分享到微信
						</a>
						<?php echo mc_shoucang_btn($val['id']); ?> 
					</div>
					<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"<?php echo $val['title']; ?>","bdMini":"2","bdMiniList":false,"bdStyle":"1","bdSize":"32"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>
					<hr>
					<?php if(mc_is_admin() || mc_is_bianji() || $author==mc_user_id()) : ?>
					<div class="text-center">
						<?php if(mc_get_page_field($val['id'],'type')=='publish') : ?>
							<?php if(mc_is_admin() || mc_is_bianji()) : ?>
								<a href="<?php echo U('home/perform/zhiding?id='.$val['id']); ?>" class="btn btn-danger btn-sm">
									<i class="glyphicon glyphicon-open"></i> 置顶
								</a> 
								<?php if(mc_get_meta($val['id'],'tuisong')) : ?>
									<a href="<?php echo U('home/perform/remts?id='.$val['id']); ?>" class="btn btn-default btn-sm">
										<i class="glyphicon glyphicon-bookmark"></i> 取消推送
									</a> 
								<?php else : ?>
									<a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#tsModal">
										<i class="glyphicon glyphicon-bookmark"></i> 推送至首页
									</a> 
								<?php endif; ?>
							<?php endif; ?>
						<?php else : ?>
						<?php if(mc_is_admin() || mc_is_bianji()) : ?>
						<form method="post" action="<?php echo U('home/perform/review'); ?>" class="inline">
						<button type="submit" class="btn btn-danger btn-sm">
							<i class="icon-ok-circle"></i> 通过审核
						</button> 
						<input type="hidden" name="id" value="<?php echo $val['id']; ?>">
						</form>
						<?php endif; ?>
						<?php endif; ?>
						<?php if(mc_is_admin() || mc_is_bianji() || $author==mc_user_id()) : ?>
						<a href="<?php echo U('publish/index/edit?id='.$val['id'].'&group='.$group_id); ?>" class="btn btn-info btn-sm">
							<i class="glyphicon glyphicon-edit"></i> 编辑
						</a> 
						<button class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal">
							<i class="glyphicon glyphicon-trash"></i> 删除
						</button>
						<?php endif; ?>
					</div>
					<hr>
					<?php endif; ?>
					<div class="media post-author">
						<div class="media-left">
							<a class="img-div img-circle" href="<?php echo mc_get_url($author); ?>">
								<img class="media-object" src="<?php echo mc_user_avatar($author); ?>" alt="<?php echo mc_user_display_name($author); ?>">
							</a>
						</div>
						<div class="media-body">
							<h4 class="media-heading mb-10">
								<a href="<?php echo mc_get_url($author); ?>"><?php echo mc_user_display_name($author); ?></a>
								<span class="label label-default">作者</span>
							</h4>
							<?php echo mc_get_page_field($author,'content'); ?>
						</div>
					</div>
					<hr>
					<?php echo W("Comment/index",array($val['id'])); ?>
				</div>
			</div>
		</div>
	</div>
	<?php if(mc_is_admin() || mc_is_group_admin(mc_get_meta($val['id'],'group'))) : ?>
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					
				</div>
				<div class="modal-body text-center">
					确认要删除这篇文章吗？
				</div>
				<div class="modal-footer" style="text-align:center;">
					<form method="post" action="<?php echo U('home/perform/delete'); ?>">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						<i class="glyphicon glyphicon-remove"></i> 取消
					</button>
					<button type="submit" class="btn btn-danger">
						<i class="glyphicon glyphicon-ok"></i> 确定
					</button>
					<input type="hidden" name="id" value="<?php echo $val['id']; ?>">
					</form>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="tsModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					
				</div>
				<form method="post" action="<?php echo U('home/perform/tuisong'); ?>">
				<div class="modal-body text-center">
					<div id="pub-imgadd">
						<img class="default-img" id="default-img" src="<?php echo mc_theme_url(); ?>/img/upload.jpg">
						<input type="hidden" name="fmimg" id="pub-input" value="">
						<input type="file" id="picfile" onchange="readFile(this,1)" />
					</div>
					<script>
						function readFile(obj,id){ 
					        var file = obj.files[0]; 	
					        //判断类型是不是图片
					        if(!/image\/\w+/.test(file.type)){   
					                alert("请确保文件为图像类型"); 
					                return false; 
					        } 
					        var reader = new FileReader(); 
					        reader.readAsDataURL(file); 
					        reader.onload = function(e){ 
					        	$('#pub-imgadd img').attr('src',this.result);
					        	$('#pub-imgadd #pub-input').val(this.result);
					            //alert(this.result);
					        } 
					} 
					</script>
				</div>
				<div class="modal-footer" style="text-align:center;">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						<i class="glyphicon glyphicon-remove"></i> 取消
					</button>
					<button type="submit" class="btn btn-danger">
						<i class="glyphicon glyphicon-ok"></i> 确定
					</button>
				</div>
				<input type="hidden" name="id" value="<?php echo $val['id']; ?>">
				</form>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	<?php endif; ?>
	<?php endforeach; ?>
	<?php if(mc_prev_page_id($val['id'])) : ?>
	<a class="prev_btn np_page_btn" href="<?php echo mc_get_url(mc_prev_page_id($val['id'])); ?>">
		<i class="fa fa-chevron-circle-left"></i>
	</a>
	<?php endif; ?>
	<?php if(mc_next_page_id($val['id'])) : ?>
	<a class="next_btn np_page_btn" href="<?php echo mc_get_url(mc_next_page_id($val['id'])); ?>">
		<i class="fa fa-chevron-circle-right"></i>
	</a>
	<?php endif; ?>
<?php mc_template_part('footer'); ?>