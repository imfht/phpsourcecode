<?php mc_template_part('header'); ?>
	<div class="container mt-40">
		<div class="panel panel-default" id="checkout">
			<!-- Default panel contents -->
			<div class="panel-heading">
				<i class="glyphicon glyphicon-map-marker"></i> 填写收货信息
			</div>
			<?php 
			if(mc_is_mobile()) :
				if(mc_option('alipay_wap_seller')) :
					$alipay_url = U('pro/alipay/alipay_wap');
				elseif(mc_option('alipay_seller')) :
					$alipay_url = U('pro/alipay/alipay');
				else : 
					$alipay_url = U('pro/alipay/alipay2');
				endif;
			elseif(mc_option('alipay_seller')) :
				$alipay_url = U('pro/alipay/alipay');
			else : 
				$alipay_url = U('pro/alipay/alipay2');
			endif;
			?>
			<form id="payment" role="form" method="post" action="<?php echo $alipay_url; ?>">
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4">
							<input type="text" name="buyer_name" class="form-control" placeholder="收货人姓名" value="<?php echo mc_get_meta(mc_user_id(),'buyer_name',true,'user'); ?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<?php if(mc_get_meta(mc_user_id(),'buyer_province',true,'user') && mc_get_meta(mc_user_id(),'buyer_city',true,'user')) : ?>
					<div class="row" id="repcbox">
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" class="form-control" value="<?php echo mc_get_meta(mc_user_id(),'buyer_province',true,'user').' '.mc_get_meta(mc_user_id(),'buyer_city',true,'user'); ?>" disabled>
								<span class="input-group-addon" id="repc">
									<i class="glyphicon glyphicon-refresh"></i>
								</span>
								<input type="hidden" name="buyer_province" value="<?php echo mc_get_meta(mc_user_id(),'buyer_province',true,'user'); ?>">
								<input type="hidden" name="buyer_city" value="<?php echo mc_get_meta(mc_user_id(),'buyer_city',true,'user'); ?>">
							</div>
						</div>
					</div>
					<script>
						$('#repc').click(function(){
							$('#repcbox').html('<div class="col-sm-2"><select class="form-control" id="province" tabindex="4" runat="server" onchange="selectprovince(this);" name="buyer_province" datatype="*" errormsg="必须选择您所在的地区"></select></div><div class="col-sm-2"><select class="form-control" id="city" tabindex="4" disabled="disabled" runat="server" name="buyer_city"></select></div>');
							jQuery.getScript("<?php echo mc_theme_url(); ?>/js/address.js");
						});
					</script>
					<?php else : ?>
					<div class="row">
						<div class="col-sm-2">
							<select class="form-control" id="province" tabindex="4" runat="server" onchange="selectprovince(this);" name="buyer_province" datatype="*" errormsg="必须选择您所在的地区"></select>
						</div>
						<div class="col-sm-2">
							<select class="form-control" id="city" tabindex="4" disabled="disabled" runat="server" name="buyer_city"></select>
						</div>
					</div>
					
					<?php endif; ?>
				</div>
				<script src="<?php echo mc_theme_url(); ?>/js/address.js"></script>
				<div class="form-group">
					<textarea class="form-control" name="buyer_address" rows="3" placeholder="区县、街道、门牌号"><?php echo mc_get_meta(mc_user_id(),'buyer_address',true,'user'); ?></textarea>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-4">
							<input type="text" class="form-control" name="buyer_phone" placeholder="联系电话，非常重要" value="<?php echo mc_get_meta(mc_user_id(),'buyer_phone',true,'user'); ?>">
						</div>
					</div>
				</div>
				<?php if(mc_is_mobile() && mc_option('alipay_wap_seller')) : ?>
				<div class="well">
					<div id="checkout-type">
						<h2 class="title">支付方式</h2>
						<ul class="sel-payment clear">
							<li class="cali">
								<input id="cali" name="bank_type" type="radio" value="0" checked="checked">
								<label class="icon-box0" for="cali">
								<img src="<?php echo mc_site_url(); ?>/pay/tenpay/image/alipay.jpg" width="135" height="32" style="border: 1px solid #ddd;">
								</label>
							</li>
							<?php if(mc_option('huodaofukuan')==2) : ?>
							<li class="chdfk">
								<input id="chdfk" type="radio" onclick="checkValue(this)" name="bank_type" value="999">
								<label class="icon-box91" for="chdfk">
								</label>
								<!--<li-->
							</li>
							<?php endif; ?>
							<div class="clearfix"></div>
						</ul>
					</div>
				</div>
				<script>	
				jQuery(document).ready(function(){
				    jQuery(".cali").click(function(){ 
				    	jQuery('#payment').attr('action','<?php echo $alipay_url; ?>');
				    });
				    jQuery(".chdfk").click(function(){ 
				    	jQuery('#payment').attr('action','<?php echo U('pro/alipay/hdfk'); ?>');
				    });
				});
				</script>
				<?php else : ?>
				<div class="well">
					<div id="checkout-type">
						<h2 class="title">支付方式</h2>
						<ul class="sel-payment clear">
							<li class="cali">
								<input id="cali" name="bank_type" type="radio" value="0" checked="checked">
								<label class="icon-box0" for="cali">
								<img src="<?php echo mc_site_url(); ?>/pay/tenpay/image/alipay.jpg" width="135" height="32" style="border: 1px solid #ddd;">
								</label>
							</li>
							<?php if(mc_option('tenpay_seller')!='') : ?>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-21" value="0">
								<label class="icon-box21" for="J-b2c_ebank-icbc105-21">
								</label>
								<!--<li-->
							</li>
							<?php endif; ?>
							<?php if(mc_option('huodaofukuan')==2) : ?>
							<li class="chdfk">
								<input id="chdfk" type="radio" onclick="checkValue(this)" name="bank_type" value="999">
								<label class="icon-box91" for="chdfk">
								</label>
								<!--<li-->
							</li>
							<?php endif; ?>
							<div class="clearfix"></div>
							<?php if(mc_option('tenpay_seller')!='') : ?>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-1" value="1002">
								<label class="icon-box1" for="J-b2c_ebank-icbc105-1">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-2" value="1001">
								<label class="icon-box2" for="J-b2c_ebank-icbc105-2">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-3" value="1003">
								<label class="icon-box3" for="J-b2c_ebank-icbc105-3">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-4" value="1005">
								<label class="icon-box4" for="J-b2c_ebank-icbc105-4">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-5" value="1052">
								<label class="icon-box5" for="J-b2c_ebank-icbc105-5">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-6" value="1028">
								<label class="icon-box6" for="J-b2c_ebank-icbc105-6">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-7" value="1004">
								<label class="icon-box7" for="J-b2c_ebank-icbc105-7">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-8" value="1027">
								<label class="icon-box8" for="J-b2c_ebank-icbc105-8">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-9" value="1022">
								<label class="icon-box9" for="J-b2c_ebank-icbc105-9">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-10" value="1006">
								<label class="icon-box10" for="J-b2c_ebank-icbc105-10">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-11" value="1021">
								<label class="icon-box11" for="J-b2c_ebank-icbc105-11">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-12" value="1009">
								<label class="icon-box12" for="J-b2c_ebank-icbc105-12">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-13" value="1010">
								<label class="icon-box13" for="J-b2c_ebank-icbc105-13">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-14" value="1008">
								<label class="icon-box14" for="J-b2c_ebank-icbc105-14">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-15" value="1020">
								<label class="icon-box15" for="J-b2c_ebank-icbc105-15">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-16" value="1032">
								<label class="icon-box16" for="J-b2c_ebank-icbc105-16">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-17" value="1054">
								<label class="icon-box17" for="J-b2c_ebank-icbc105-17">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-18" value="1056">
								<label class="icon-box18" for="J-b2c_ebank-icbc105-18">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-19" value="1082">
								<label class="icon-box19" for="J-b2c_ebank-icbc105-19">
								</label>
								<!--<li-->
							</li>
							<li class="cten">
								<input type="radio" onclick="checkValue(this)" name="bank_type" id="J-b2c_ebank-icbc105-20" value="1076">
								<label class="icon-box20" for="J-b2c_ebank-icbc105-20">
								</label>
								<!--<li-->
							</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
				<script>	
				jQuery(document).ready(function(){
				    jQuery(".cali").click(function(){ 
				    	jQuery('#payment').attr('action','<?php echo $alipay_url; ?>');
				    });
				    jQuery(".cten").click(function(){ 
				    	jQuery('#payment').attr('action','<?php echo U('pro/alipay/tenpay'); ?>');
				    });
				    jQuery(".chdfk").click(function(){ 
				    	jQuery('#payment').attr('action','<?php echo U('pro/alipay/hdfk'); ?>');
				    });
				});
				</script>
				<?php endif; ?>
				<div class="well">
					<h2 class="title">积分抵现 <small>100积分可抵现1元人民币，最多抵现订单总额的50%</small></h2>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<input onkeyup="value=value.replace(/[^0-9]/g,'')" onpaste="value=value.replace(/[^0-9]/g,'')" oncontextmenu = "value=value.replace(/[^0-9]/g,'')" type="text" id="coins-input" class="form-control" name="coins" placeholder="使用积分数量" value="">
							</div>
							<ul class="list-inline">
								<li>现有积分：<?php echo mc_coins(mc_user_id()); ?></li>
								<li id="coins-after" style="display:none">抵现后积分：<span><?php echo mc_coins(mc_user_id()); ?></span></li>
							</ul>
						</div>
						<div class="col-sm-8 text-right">
							<?php if(mc_option('yunfei')>0) : ?>
							<p>商品总价：<?php echo mc_total()-mc_option('yunfei'); ?> 元</p>
							<p>运费：<?php echo mc_option('yunfei'); ?> 元</p>
							<style>#total-true {padding-top: 0;}</style>
							<?php endif; ?>
							<p id="total-true">订单总额：<span><?php echo mc_total(); ?></span> 元</p>
						</div>
					</div>
				</div>
				<script>	
				jQuery(document).ready(function(){
				    $('#coins-input').keyup(function(){
				    	var coins = $(this).val()*1;
				    	if(coins><?php echo mc_coins(mc_user_id()); ?> || coins><?php echo mc_total()*50; ?>) {
					    	$(this).val('');
					    	$('#total-true span').text(<?php echo mc_total(); ?>);
					    	$('#coins-after').css('display','none');
				    	} else {
					    	$('#total-true span').text(<?php echo mc_total(); ?>-coins/100);
					    	$('#coins-after span').text(<?php echo mc_coins(mc_user_id()); ?>-coins);
					    	if(coins>0) {
						    	$('#coins-after').css('display','inline');
					    	} else {
					    		$('#coins-after').css('display','none');
					    	}
				    	}
				    });
				});
				</script>
			</div>
			<div class="panel-footer">
				<a href="<?php echo U('pro/cart/index'); ?>" class="btn btn-default">
					<i class="glyphicon glyphicon-circle-arrow-left"></i> 上一步
				</a>
				<button type="submit" class="btn btn-warning pull-right">
					<i class="glyphicon glyphicon-usd"></i> 立即支付
				</button>
			</div>
			</form>
		</div>
	</div>
<?php mc_template_part('footer'); ?>