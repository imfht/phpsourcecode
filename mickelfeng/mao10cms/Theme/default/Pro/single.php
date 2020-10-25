<?php mc_template_part('header'); ?>
<?php foreach($page as $val) : ?>
<div class="container mt-40 pro-single">
	<h1 class="title mb-20">
		<?php echo $val['title']; ?>
		<?php echo mc_shoucang_btn($val['id']); ?> 
	</h1>
	<div id="pro-index-tlin">
		<?php $fmimg_args = mc_get_meta($val['id'],'fmimg',false); $fmimg_args = array_reverse($fmimg_args); ?>
		<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
			<?php if(count($fmimg_args)>1) : ?>
			<ol class="carousel-indicators">
				<?php foreach($fmimg_args as $fmimg) : ?>
				<?php $fmimg_num++; ?>
				<li data-target="#carousel-example-generic" data-slide-to="<?php echo $fmimg_num-1; ?>" class="<?php if($fmimg_num==1) echo 'active'; ?>"></li>
				<?php endforeach; ?>
			</ol>
			<?php endif; ?>
			<div class="carousel-inner">
				<?php $fmimg_num=0; ?>
				<?php foreach($fmimg_args as $fmimg) : ?>
				<?php $fmimg_num++; ?>
				<div class="item <?php if($fmimg_num==1) echo 'active'; ?>">
					<div class="imgshow"><img src="<?php echo $fmimg; ?>" alt="<?php echo $val['title']; ?>"></div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<div id="pro-index-trin">
		<div class="h3s">
			<span id="price" price-data="<?php echo mc_get_meta($val['id'],'price'); ?>"><?php echo mc_price_now($val['id']); ?></span> 元
			<ul class="list-inline">
				<li>库存：<span id="kucun"><?php echo mc_kucun_now($val['id']); ?></span></li>
				<li>总销量：<?php if(mc_get_meta($val['id'],'xiaoliang')) : echo mc_get_meta($val['id'],'xiaoliang'); else : echo '0'; endif; ?></li>
				<li>分类：<?php $term_id = mc_get_meta($val['id'],'term'); $parent = mc_get_meta($term_id,'parent',true,'term'); if($parent) : ?><a href="<?php echo U('pro/index/term?id='.$parent); ?>"><?php echo mc_get_page_field($parent,'title'); ?></a> - <?php endif; ?><a href="<?php echo U('pro/index/term?id='.$term_id); ?>"><?php echo mc_get_page_field($term_id,'title'); ?></a></li>
			</ul>
		</div>
					<form method="post" action="<?php echo U('home/perform/add_cart'); ?>" id="pro-single-form">
					<?php 
						$parameter = unserialize(mc_get_meta($val['id'],'parameter'));
						if($parameter) :
					?>
					<hr>
					<h4 class="title mb-20">选择商品参数：</h4>
					<ul class="list-inline pro-par-list">
					<?php foreach($parameter as $key_par=>$val_par) : $num_par++; ?>
						<?php 
							$name = $val_par['name'];
							$price = $val_par['price'];
							$kucun = $val_par['kucun'];
							if($kucun>0) :
						?>
						<li>
							<label <?php if($num_par==1) echo 'class="active"'; ?> price-data="<?php if($price) : echo $price; else : echo '0'; endif; ?>" kucun-data="<?php if($kucun) : echo $kucun; else : echo '0'; endif; ?>">
								<span><?php echo $name; ?></span>
								<input type="radio" name="parameter" value="<?php echo $key_par; ?>"  <?php if($num_par==1) echo 'checked'; ?>>
							</label>
						</li>
					<?php endif; endforeach; ?>
					</ul>
					<script>
						$('.pro-par-list label').click(function(){
							$('.pro-par-list label').removeClass('active');
							$(this).addClass('active');
							//价格
							var price_now = $(this).attr('price-data')*1;
							$('#price').text(price_now);
							//库存
							var kucun_now = $(this).attr('kucun-data')*1;
							$('#kucun').text(kucun_now);
							if(kucun_now>0) {
								$('.buy-btn-1').css('display','block');
								$('.buy-btn-2').css('display','none');
							} else {
								$('.buy-btn-1').css('display','none');
								$('.buy-btn-2').css('display','block');
							};
						});
					</script>
					<?php endif; ?>
					<hr>
					<div class="form-group">
						<div class="btn-group buy-btn-2" style="display:<?php if(mc_kucun_now($val['id'])<=0) : ?>block<?php else : ?>none<?php endif; ?>">
							<button type="button" class="btn btn-default">
								<i class="fa fa-umbrella"></i> 暂时缺货
							</button>
						</div>
						<div class="btn-group buy-btn-1" style="display:<?php if(mc_kucun_now($val['id'])<=0) : ?>none<?php else : ?>block<?php endif; ?>">
							<?php if(mc_user_id()) : ?>
							<button type="submit" class="btn btn-default mr-10" id="add-cart-btn-1">
								<i class="glyphicon glyphicon-shopping-cart"></i> 加入购物车
							</button>
							<button type="submit" class="btn btn-warning add-cart" id="add-cart-btn-2">
								<i class="glyphicon glyphicon-check"></i> 立即购买
							</button>
							<?php else : ?>
							<button type="button" class="btn btn-default mr-10" data-toggle="modal" data-target="#loginModal">
								<i class="glyphicon glyphicon-shopping-cart"></i> 加入购物车
							</button>
							<button type="button" class="btn btn-warning add-cart" data-toggle="modal" data-target="#loginModal">
								<i class="glyphicon glyphicon-check"></i> 立即购买
							</button>
							<?php endif; ?>
							<?php if(mc_get_meta($val['id'],'tb_name') && mc_get_meta($val['id'],'tb_url')) : ?>
							<a class="btn btn-default ml-10" target="_blank" rel="nofollow" href="<?php echo mc_get_meta($val['id'],'tb_url'); ?>">
								<i class="glyphicon glyphicon-send"></i> 去<?php echo mc_get_meta($val['id'],'tb_name'); ?>购买
							</a>
							<?php endif; ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<script>
						$('#add-cart-btn-1').hover(function(){
							$('#add-cart-back').val('1');
						});
						$('#add-cart-btn-2').hover(function(){
							$('#add-cart-back').val('0');
						});
					</script>
					<input type="hidden" value="1" name="number">
					<input type="hidden" id="add-cart-back" value="0" name="back">
					<input type="hidden" value="<?php echo $val['id']; ?>" name="id">
					</form>
					</div>
<!-- 商品头部结束 -->
	<div class="clearfix"></div>
	<div id="entry" class="mt-40">
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
					</div>
					<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"<?php echo $val['title']; ?>","bdMini":"2","bdMiniList":false,"bdPic":"<?php echo $fmimg_args[0]; ?>","bdStyle":"1","bdSize":"32"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>
					<hr>
	<?php 
		$args_id = M('meta')->where("meta_key='term' AND meta_value='$term_id' AND type='basic' AND page_id!='".$val['id']."'")->getField('page_id',true);
		if($args_id) :
		$condition['id']  = array('in',$args_id);
		$condition['type'] = 'pro';
	    $page_term = M('page')->where($condition)->order('date desc')->page(1,4)->select();
	    endif;
	    if($page_term) :
	?>
	<div class="home-pro">
		<h4 class="title mb-10">
			<i class="glyphicon glyphicon-star"></i> 推荐商品
		</h4>
		<div class="row mb-20">
			<?php foreach($page_term as $val_term) : ?>
			<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col">
				<a class="pr img-div" href="<?php echo mc_get_url($val_term['id']); ?>">
					<?php $fmimg_args = mc_get_meta($val_term['id'],'fmimg',false); $fmimg_args = array_reverse($fmimg_args); ?>
					<img src="<?php echo $fmimg_args[0]; ?>">
					<div class="pa txt">
						<span class="wto"><?php echo $val_term['title']; ?></span>
						<?php echo mc_price_now($val_term['id']); ?> <small>元</small>
					</div>
					<div class="pa bg"></div>
				</a>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>
	<div id="pro-single">
		<div class="row">
			<div class="col-sm-12 pt-0" id="single">
				<?php echo W("Comment/index",array($val['id'])); ?>
			</div>
		</div>
	</div>
	</div>
	<?php if(mc_is_admin()) : ?>
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title" id="myModalLabel">
						
					</h4>
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