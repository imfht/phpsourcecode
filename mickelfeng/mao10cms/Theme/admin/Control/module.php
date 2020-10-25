<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<form role="form" method="post" action="<?php echo U('user/index/site_control'); ?>">
					<div class="form-group">
						<label>
							首页SEO设置
						</label>
						<input type="text" name="home_title" class="form-control" placeholder="首页标题" value="<?php echo mc_option('home_title'); ?>">
					</div>
					<div class="form-group">
						<input type="text" name="home_keywords" class="form-control" placeholder="首页关键词,英文半角逗号隔开" value="<?php echo mc_option('home_keywords'); ?>">
					</div>
					<div class="form-group">
						<input type="text" name="home_description" class="form-control" placeholder="首页描述" value="<?php echo mc_option('home_description'); ?>">
					</div>
					<div class="form-group">
						<label>
							商品频道首页SEO设置
						</label>
						<input type="text" name="pro_title" class="form-control" placeholder="页面标题" value="<?php echo mc_option('pro_title'); ?>">
					</div>
					<div class="form-group">
						<input type="text" name="pro_keywords" class="form-control" placeholder="页面关键词,英文半角逗号隔开" value="<?php echo mc_option('pro_keywords'); ?>">
					</div>
					<div class="form-group">
						<input type="text" name="pro_description" class="form-control" placeholder="页面描述" value="<?php echo mc_option('pro_description'); ?>">
					</div>
					<div class="form-group">
						<label>
							社区首页SEO设置
						</label>
						<input type="text" name="group_title" class="form-control" placeholder="页面标题" value="<?php echo mc_option('group_title'); ?>">
					</div>
					<div class="form-group">
						<input type="text" name="group_keywords" class="form-control" placeholder="页面关键词,英文半角逗号隔开" value="<?php echo mc_option('group_keywords'); ?>">
					</div>
					<div class="form-group">
						<input type="text" name="group_description" class="form-control" placeholder="页面描述" value="<?php echo mc_option('group_description'); ?>">
					</div>
					<div class="form-group">
				        <label>
				            新建主题审核
				        </label>
				        <div class="clearfix"></div>
				        <label class="radio-inline">
							<input type="radio" name="shenhe_post" value="1" <?php if(mc_option('shenhe_post')!=2) : ?>checked<?php endif; ?>>
							无须审核
						</label>
				        <label class="radio-inline">
							<input type="radio" name="shenhe_post" value="2" <?php if(mc_option('shenhe_post')==2) : ?>checked<?php endif; ?>>
							需要审核
						</label>
				    </div>
				    <div class="form-group">
				        <label>
				            社区主题排序方式
				        </label>
				        <div class="clearfix"></div>
				        <label class="radio-inline">
							<input type="radio" name="paixu" value="1" <?php if(mc_option('paixu')!=2) : ?>checked<?php endif; ?>>
							点击排序
						</label>
				        <label class="radio-inline">
							<input type="radio" name="paixu" value="2" <?php if(mc_option('paixu')==2) : ?>checked<?php endif; ?>>
							评论排序
						</label>
				    </div>
				    <div class="form-group">
				    	<label>
							文章频道首页SEO设置
						</label>
						<input type="text" name="article_title" class="form-control" placeholder="页面标题" value="<?php echo mc_option('article_title'); ?>">
					</div>
					<div class="form-group">
						<input type="text" name="article_keywords" class="form-control" placeholder="页面关键词,英文半角逗号隔开" value="<?php echo mc_option('article_keywords'); ?>">
					</div>
					<div class="form-group">
						<input type="text" name="article_description" class="form-control" placeholder="页面描述" value="<?php echo mc_option('article_description'); ?>">
					</div>
					<input name="the_control" type="hidden" value="ok">
					<button type="submit" class="btn btn-warning btn-block">
						<i class="glyphicon glyphicon-ok-circle"></i> 保存
					</button>
				</form>
	</div>
<?php mc_template_part('footer'); ?>