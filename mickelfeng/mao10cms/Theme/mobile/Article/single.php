<?php mc_template_part('header'); ?>
<?php foreach($page as $val) : ?>
	<div class="container-fluid article-single">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
				<div id="article-single">
					<h1 class="title mt-10"><?php echo $val['title']; ?></h1>
					<ul id="tags" class="list-inline">
						<?php if(mc_get_meta($val['id'],'tag',false)) : ?>
						<li><i class="glyphicon glyphicon-tags"></i></li>
						<?php foreach(mc_get_meta($val['id'],'tag',false) as $tag) : ?>
						<li><a href="<?php echo U('article/index/tag?tag='.$tag); ?>"><?php echo $tag; ?></a></li>
						<?php endforeach; ?>
						<?php endif; ?>
						<li><i class="glyphicon glyphicon-th-list"></i></li>
						<li>
							<a href="<?php echo U('article/index/term?id='.mc_get_meta($val['id'],'term')); ?>">
								<?php echo mc_get_page_field(mc_get_meta($val['id'],'term'),'title'); ?>
							</a>
						</li>
					</ul>
					<div id="entry">
						<?php echo mc_magic_out($val['content']); ?>
					</div>
					<hr>
					<?php echo W("Comment/index",array($val['id'])); ?>
				</div>
			</div>
		</div>
	</div>
<?php endforeach; ?>
<?php mc_template_part('footer'); ?>