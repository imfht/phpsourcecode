<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<div class="home-main" id="images">
			<div class="row">
				<?php foreach($content as $val) : ?>
				<div class="col-sm-6 col-md-4 col-lg-3 col mt-20">
					<div class="img-div">
						<img src="<?php echo $val['src']; ?>">
					</div>
					<?php
						$condition_page['content'] = array('like', "%{$val['src']}%");
						$page_images = M('page')->where($condition_page)->getField('id');
						$condition_meta['meta_value'] = array('like', "%{$val['src']}%");
						$meta_images = M('meta')->where($condition_meta)->getField('id');
						$condition_option['meta_value'] = array('like', "%{$val['src']}%");
						$option_images = M('option')->where($condition_option)->getField('id');
						$image_used = $page_images+$meta_images+$option_images;
					?>
					<?php if($image_used>0) : ?>
					<a class="btn btn-default btn-block btn-sm" href="javascript:;">图片使用中</a>
					<?php else : ?>
					<form method="post" action="<?php echo U('home/perform/delete_img'); ?>">
					<button type="submit" class="btn btn-warning btn-block btn-sm">
						未使用，删除！
					</button>
					<input type="hidden" name="id" value="<?php echo $val['id']; ?>">
					</form>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
			<?php echo mc_pagenavi($count,$page_now,20); ?>
		</div>
	</div>
<?php mc_template_part('footer'); ?>