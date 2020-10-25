<?php mc_template_part('header'); ?>
<div class="container-fluid home-pro mt-40">
	<?php 
		$args_id = M('meta')->where("meta_key='parent' AND meta_value>'0' AND type='term'")->getField('page_id',true);
		if($args_id) :
		$condition['id']  = array('not in',$args_id);
		$condition['type']  = 'term_pro';
		$terms_pro = M('page')->where($condition)->order('date desc')->select(); 
		if($terms_pro) :
	?>
	<div class="nav-terms mb-20">
		<ul class="list-inline mb-0">
			<li role="presentation" class="head active">
				<a href="javascript:;">
					全部商品
				</a>
			</li>
		</ul>
		<?php foreach($terms_pro as $val) : ?>
		<ul class="list-inline mb-0">
			<li role="presentation" class="head">
				<a href="<?php echo U('pro/index/term?id='.$val['id']); ?>">
					<?php echo $val['title']; ?>
				</a>
			</li>
			<?php 
				$args_id_t = M('meta')->where("meta_key='parent' AND meta_value='".$val['id']."' AND type='term'")->getField('page_id',true);
				if($args_id_t) :
				$condition_t['id']  = array('in',$args_id_t);
				$condition_t['type']  = 'term_pro';
				$terms_pro_t = M('page')->where($condition_t)->order('date desc')->select(); 
				endif;
				if($terms_pro_t) :
				foreach($terms_pro_t as $val_t) :
			?>
			<li role="presentation">
				<a href="<?php echo U('pro/index/term?id='.$val_t['id']); ?>">
					<?php echo $val_t['title']; ?>
				</a>
			</li>
			<?php endforeach; endif; ?>
		</ul>
		<?php endforeach; ?>
	</div>
	<?php 
		endif;
		else :
		$condition['type']  = 'term_pro';
		$terms_pro = M('page')->where($condition)->order('date desc')->select(); 
		if($terms_pro) :
	?>
	<div class="nav-terms mb-20">
		<ul class="list-inline mb-0">
			<li role="presentation" class="active">
				<a href="javascript:;">
					全部商品
				</a>
			</li>
			<?php foreach($terms_pro as $val) : ?>
			<li role="presentation">
				<a href="<?php echo U('pro/index/term?id='.$val['id']); ?>">
					<?php echo $val['title']; ?>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; endif; ?>
	<div class="row">
		<?php foreach($page as $val) : ?>
		<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col">
			<a class="pr img-div" href="<?php echo mc_get_url($val['id']); ?>">
				<?php $fmimg_args = mc_get_meta($val['id'],'fmimg',false); $fmimg_args = array_reverse($fmimg_args); ?>
				<img src="<?php echo $fmimg_args[0]; ?>">
				<div class="pa txt">
					<span class="wto"><?php echo $val['title']; ?></span>
					<?php echo mc_price_now($val['id']); ?> <small>元</small>
				</div>
				<div class="pa bg"></div>
			</a>
		</div>
		<?php endforeach; ?>
	</div>
	<?php echo mc_pagenavi($count,$page_now); ?>
</div>
<?php mc_template_part('footer'); ?>