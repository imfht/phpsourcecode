<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<div class="row">
			<div class="col-lg-12" id="pro-all-list">
				<?php foreach($page as $val) : $pro_id = mc_get_meta($val['page_id'],'group'); ?>
				<div class="panel panel-default">
				<div class="panel-heading">
					<a href="<?php echo mc_get_url($val['page_id']); ?>"><?php echo mc_get_page_field($val['page_id'],'title'); ?></a>
				</div>
				<ul class="list-group" id="cart" style="border:0">
				<li class="list-group-item pr">
					<div class="media">
						<a class="pull-left img-div" href="<?php echo U('pro/index/single?id='.$pro_id); ?>">
							<?php $fmimg_args = mc_get_meta($pro_id,'fmimg',false); ?>
							<img class="media-object" src="<?php echo $fmimg_args[0]; ?>" alt="<?php echo mc_get_page_field($pro_id,'title'); ?>">
						</a>
						<div class="media-body">
							<h4 class="media-heading">
								<a href="<?php echo U('pro/index/single?id='.$pro_id); ?>"><?php echo mc_get_page_field($pro_id,'title'); ?></a>
							</h4>
							<span class="btn btn-danger btn-sm">总价：<?php echo mc_total_wish_for($val['id']); ?> 元</span>
							<span class="btn btn-info btn-sm">数量：<?php echo mc_get_meta($val['page_id'],'number'); ?></span>
							<?php $parameter = mc_get_meta($val['page_id'],'parameter',false); if($parameter) : ?>
							<ul class="list-inline mt-10">
							<?php foreach($parameter as $par) : list($key,$par_value) = explode('|',$par); $par_name = M('option')->where("id='$key'")->getField('meta_value'); ?>
							<li><span class="btn btn-success btn-sm"><?php echo $par_name; ?>：<?php echo $par_value; ?></span></li>
							<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</div>
					</div>
					<div class="cart-status wait_finished">
						心愿达成
					</div>
				</li>
				</ul>
				<div class="panel-footer">
					<h4><?php echo mc_get_meta($val['page_id'],'buyer_name'); ?></h4>
					<p><?php echo mc_get_meta($val['page_id'],'buyer_province'); ?> , <?php echo mc_get_meta($val['page_id'],'buyer_city'); ?> , <?php echo mc_get_meta($val['page_id'],'buyer_address'); ?></p>
					<p><?php echo mc_get_meta($val['page_id'],'buyer_phone'); ?></p>
				</div>
				</div>
				<?php endforeach; ?>
				<?php echo mc_pagenavi($count,$page_now); ?>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>