<?php mc_template_part('header'); ?>
<?php mc_template_part('head-user'); ?>
<?php mc_template_part('head-user-nav'); ?>
	<div class="container">
		<div id="home-main-u" class="home-main">
			<div class="row">
				<div class="col-md-8 col-lg-9" id="post-list-default">
					<h4 class="title">
						<i class="fa fa-th-large"></i> 动态
					</h4>
					<?php if($page) : ?>
					<ul class="list-group">
					<?php foreach($page as $val) : ?>
					<?php 
						$page_id =  $val['page_id'];
						$user_id = $val['user_id'];
						$key = $val['action_key'];
						$value = $val['action_value']; 
					?>
					<li class="list-group-item" id="mc-page-<?php echo $val['id']; ?>">
						<div class="media">
							<a class="media-left" href="<?php echo mc_get_url($user_id); ?>">
								<div class="img-div img-circle">
									<img class="media-object" src="<?php echo mc_user_avatar($user_id); ?>" alt="<?php echo mc_user_display_name($user_id); ?>">
								</div>
							</a>
							<div class="media-body">
								<h4 class="media-heading mb-10">
									<?php if($user_id==mc_user_id()) : ?>
									你 
									<?php else : ?>
									<a href="<?php echo U('user/index/index?id='.$user_id); ?>"><?php echo mc_user_display_name($user_id); ?></a> 
									<?php endif; ?>
									<?php if($key=='at') : 
										$comment_page_id = mc_comment_page($value);
										if($page_id==mc_user_id()) : ?>
										在 <a href="<?php echo mc_get_url($comment_page_id); ?>#comment-<?php echo $value; ?>"><?php echo mc_get_page_field($comment_page_id,'title'); ?></a> 中，@了 你：
										<?php else : ?>
										在 <a href="<?php echo mc_get_url($comment_page_id); ?>#comment-<?php echo $value; ?>"><?php echo mc_get_page_field($comment_page_id,'title'); ?></a> 中，@了 <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php endif; ?>
									<?php elseif($key=='publish') :
										$type = mc_get_page_field($page_id,'type');
										if($type=='publish') :
										$group = mc_get_meta($page_id,'group'); ?>
										在 <a href="<?php echo mc_get_url($group); ?>"><?php echo mc_get_page_field($group,'title'); ?></a> 中发布了新主题 <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php elseif($type=='group') : ?>
										新建了群组 <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php elseif($type=='article') : ?>
										发布了文章 <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php elseif($type=='pro') : ?>
										发布了商品 <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php endif; ?>
									<?php elseif($key=='perform') :
										$type = mc_get_page_field($page_id,'type');
										if($type=='publish') :
											$name = '主题';
										elseif($type=='group') :
											$name = '群组';
										elseif($type=='article') :
											$name = '文章';
										elseif($type=='pro') :
											$name = '商品';
										endif;
										if($value=='xihuan') : ?>
										喜欢了<?php echo $name; ?> <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php elseif($value=='shoucang') : ?>
										收藏了<?php echo $name; ?> <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php elseif($value=='guanzhu') : ?>
											<?php if($page_id==mc_user_id()) : ?>
												关注了你
											<?php else : ?>
												关注了用户 <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>
											<?php endif; ?>
										<?php endif; ?>
									<?php elseif($key=='comment') : ?>
										<?php $author = mc_get_meta($page_id,'author',true); ?>
										<?php if($author==mc_user_id()) : ?>
										<?php
											$type = mc_get_page_field($page_id,'type');
											if($type=='publish') :
												$name = '主题';
											elseif($type=='group') :
												$name = '群组';
											elseif($type=='article') :
												$name = '文章';
											elseif($type=='pro') :
												$name = '商品';
											endif;
										?>
										评论了您的<?php echo $name; ?> <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php else : ?>
										在 <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a> 中，发表了评论：
										<?php endif; ?>
									<?php elseif($key=='comment2') : ?>
										<?php $page_id = M('action')->where("id='$page_id' AND action_key='comment'")->getField('page_id'); ?>
										<?php
											$type = mc_get_page_field($page_id,'type');
											if($type=='publish') :
												$name = '主题';
											elseif($type=='group') :
												$name = '群组';
											elseif($type=='article') :
												$name = '文章';
											elseif($type=='pro') :
												$name = '商品';
											endif;
										?>
										在<?php echo $name; ?> <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a> 中，回复：
									<?php elseif($key=='wish') : ?>
										<?php $author = mc_get_meta($page_id,'author',true); ?>
										<?php if($author==mc_user_id()) : ?>
										支持了你的心愿 <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php else : ?>
										支持了心愿 <a href="<?php echo mc_get_url($page_id); ?>"><?php echo mc_get_page_field($page_id,'title'); ?></a>：
										<?php endif; ?>
									<?php endif; ?>
								</h4>
								<?php if($key=='at') : ?>
								<p><?php echo M('action')->where("id='$value' AND action_key='comment'")->getField('action_value'); ?></p>
								<?php elseif($key=='publish') : ?>
									<?php if($type=='pro') : ?>
									<p><a href="<?php echo mc_get_url($page_id); ?>"><img src="<?php echo mc_fmimg($page_id); ?>"</a></p>
									<?php else : ?>
									<p><?php echo mc_cut_str(strip_tags(mc_magic_out(mc_get_page_field($page_id,'content'))), 250); ?></p>
									<?php endif; ?>
								<?php elseif($key=='perform') : ?>
									<?php if($value=='xihuan' || $value=='shoucang') : ?>
										<?php if($type=='pro') : ?>
										<p><a href="<?php echo mc_get_url($page_id); ?>"><img src="<?php echo mc_fmimg($page_id); ?>"</a></p>
										<?php else : ?>
										<p><?php echo mc_cut_str(strip_tags(mc_magic_out(mc_get_page_field($page_id,'content'))), 250); ?></p>
										<?php endif; ?>
									<?php endif; ?>
								<?php elseif($key=='comment' || $key=='comment2') : ?>
								<div class="mb-10"><?php echo mc_magic_out($value); ?></div>
								<?php elseif($key=='wish') : ?>
								<p>支持金额：<span class="text-danger"><?php echo $value; ?></span>元</p>
								<?php endif; ?>
								<p class="post-info">
									<i class="glyphicon glyphicon-time"></i><?php echo date('Y-m-d H:i:s',$val['date']); ?>
								</p>
							</div>
						</div>
					</li>
					<?php endforeach; ?>
					</ul>
					<?php echo mc_pagenavi($count,$page_now); ?>
					<?php else : ?>
					<div id="nothing">
						还没有关于您的任何动态，去<a href="<?php echo mc_site_url(); ?>">网站首页</a>逛逛吧！
					</div>
					<?php endif; ?>
				</div>
				<div class="col-md-4 col-lg-3 hidden-xs hidden-sm home-side">
					<?php if(mc_option('pro_close')!=1) : ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-th-list"></i> 最新商品 
							<a class="pull-right" href="<?php echo U('pro/index/index'); ?>"><i class="fa fa-angle-right"></i></a>
						</div>
						<?php $newprob = M('page')->where('type="pro"')->order('id desc')->page(1,2)->select(); ?>
						<?php if($newprob) : ?>
						<ul class="list-group">
							<?php foreach($newprob as $val) : ?>
							<li class="list-group-item">
								<div class="media">
									<a class="media-left" href="<?php echo mc_get_url($val['id']); ?>">
										<?php $fmimg_args = mc_get_meta($val['id'],'fmimg',false); ?>
										<div class="img-div">
											<img class="media-object" src="<?php echo $fmimg_args[0]; ?>" alt="<?php echo $val['title']; ?>">
										</div>
									</a>
									<div class="media-body">
										<h4 class="media-heading">
											<a href="<?php echo mc_get_url($val['id']); ?>"><?php echo $val['title']; ?></a>
										</h4>
										<p><span><?php echo mc_get_meta($val['id'],'price'); ?></span> <small>元</small></p>
									</div>
								</div>
							</li>
							<?php endforeach; ?>
						</ul>
						<?php else : ?>
						<div class="panel-body">
							暂时没有任何商品
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<?php if(mc_option('group_close')!=1) : ?>
					<div class="panel panel-default home-side-post">
						<div class="panel-heading">
							<i class="fa fa-th-list"></i> 最新话题
							<a class="pull-right" href="<?php echo U('post/group/index'); ?>"><i class="fa fa-angle-right"></i></a>
						</div>
						<?php 
							$newpost = M('page')->where("type='publish'")->order('id desc')->page(1,3)->select();
							if($newpost) :
						?>
						<ul class="list-group">
							<?php foreach($newpost as $val) : ?>
							<?php $author = mc_get_meta($val['id'],'author',true); ?>
							<li class="list-group-item">
								<div class="media">
									<a class="media-left" href="<?php echo mc_get_url($author); ?>">
										<div class="img-div img-circle">
											<img class="media-object" src="<?php echo mc_user_avatar($author); ?>" alt="<?php echo mc_user_display_name($author); ?>">
										</div>
									</a>
									<div class="media-body">
										<h4 class="media-heading">
											<a href="<?php echo mc_get_url($val['id']); ?>"><?php echo $val['title']; ?></a>
										</h4>
									</div>
								</div>
							</li>
							<?php endforeach; ?>
						</ul>
						<?php else : ?>
						<div class="panel-body">
							暂时没有任何分享
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<?php if(mc_option('article_close')!=1) : ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-th-list"></i> 最新文章
							<a class="pull-right" href="<?php echo U('article/index/index'); ?>"><i class="fa fa-angle-right"></i></a>
						</div>
						<?php $newarticle = M('page')->where("type='article'")->order('id desc')->page(1,3)->select(); if($newarticle) : ?>
						<div class="list-group">
							<?php foreach($newarticle as $val) : ?>
							<a href="<?php echo mc_get_url($val['id']); ?>" class="list-group-item">
								<?php echo $val['title']; ?>
							</a>
							<?php endforeach; ?>
						</div>
						<?php else : ?>
						<div class="panel-body">
							暂时没有任何文章
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>