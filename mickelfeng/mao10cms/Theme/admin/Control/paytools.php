<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<div class="row">
			<div class="col-lg-12">
				<div id="single">
				<p class="text-right">
					<a rel="nofollow" href="https://b.alipay.com/order/productSet.htm" target="_blank" class="btn btn-default btn-sm">支付宝接口申请</a> 
					<a rel="nofollow" href="http://mch.tenpay.com/market/ps_index.shtml" target="_blank" class="btn btn-default btn-sm">财付通接口申请</a>
				</p>
				<form role="form" method="post" action="<?php echo mc_page_url(); ?>">
					<input type="hidden" name="update_paytools" value="ok">
				    <div class="form-group">
				        <label>
				            支付宝担保交易接口设置
				        </label>
				        <input name="alipay2_seller" type="text" class="form-control" value="<?php echo mc_option('alipay2_seller'); ?>" placeholder="支付宝卖家账户">
				    </div>
				    <div class="form-group">
				        <input name="alipay2_partner" type="text" class="form-control" value="<?php echo mc_option('alipay2_partner'); ?>" placeholder="Partner">
				    </div>
				    <div class="form-group">
				        <input name="alipay2_key" type="text" class="form-control password" value="<?php echo mc_option('alipay2_key'); ?>" placeholder="Key">
				        <p class="help-block">
				            此接口设置，仅针对支付宝担保交易接口，其他接口无效。
				        </p>
				    </div>
				    <div class="form-group">
				        <label>
				            支付宝即时到帐接口设置
				        </label>
				        <input name="alipay_seller" type="text" class="form-control" value="<?php echo mc_option('alipay_seller'); ?>" placeholder="支付宝卖家账户">
				    </div>
				    <div class="form-group">
				        <input name="alipay_partner" type="text" class="form-control" value="<?php echo mc_option('alipay_partner'); ?>" placeholder="Partner">
				    </div>
				    <div class="form-group">
				        <input name="alipay_key" type="text" class="form-control password" value="<?php echo mc_option('alipay_key'); ?>" placeholder="Key">
				        <p class="help-block">
				            此接口设置，仅针对支付宝即时到帐接口，其他接口无效。
				        </p>
				    </div>
				    <div class="form-group">
				        <label>
				            支付宝WAP接口设置
				        </label>
				        <input name="alipay_wap_seller" type="text" class="form-control" value="<?php echo mc_option('alipay_wap_seller'); ?>" placeholder="支付宝卖家账户">
				    </div>
				    <div class="form-group">
				        <input name="alipay_wap_partner" type="text" class="form-control" value="<?php echo mc_option('alipay_wap_partner'); ?>" placeholder="Partner">
				    </div>
				    <div class="form-group">
				        <input name="alipay_wap_key" type="text" class="form-control password" value="<?php echo mc_option('alipay_wap_key'); ?>" placeholder="Key">
				        <p class="help-block">
				            此接口设置，仅针对支付宝WAP接口，其他接口无效。
				        </p>
				    </div>
				    <div class="form-group">
				        <label>
				            财付通即时到帐接口设置
				        </label>
				        <input name="tenpay_seller" type="text" class="form-control" value="<?php echo mc_option('tenpay_seller'); ?>" placeholder="财付通商户号">
				    </div>
				    <div class="form-group">
				        <input name="tenpay_key" type="text" class="form-control password" value="<?php echo mc_option('tenpay_key'); ?>" placeholder="财付通密钥">
				        <p class="help-block">
				            此接口设置，仅针对财付通即时到帐接口，其他接口无效。
				        </p>
				    </div>
				    <div class="form-group">
				        <label>
				            货到付款设置
				        </label>
				        <div class="clearfix"></div>
				        <label class="radio-inline">
							<input type="radio" name="huodaofukuan" value="1" <?php if(mc_option('huodaofukuan')!=2) echo 'checked'; ?>>
							关闭
						</label>
						<label class="radio-inline">
							<input type="radio" name="huodaofukuan" value="2" <?php if(mc_option('huodaofukuan')==2) echo 'checked'; ?>>
							开启
						</label>
				    </div>
				    <div class="form-group">
				        <label>
				            运费设置
				        </label>
				        <input name="yunfei" type="text" class="form-control" value="<?php echo mc_option('yunfei'); ?>" placeholder="大于0的整数">
				    </div>
				    <div class="form-group">
				        <label>
				            购物获得积分比例设置
				        </label>
				        <div class="input-group">
				        	<input name="jifen" type="text" class="form-control" value="<?php echo mc_option('jifen'); ?>" placeholder="大于0小于100的整数">
							<span class="input-group-addon">
								%
							</span>
						</div>
				    </div>
				    <div class="text-center">
					    <button type="submit" class="btn btn-warning">
					        <i class="glyphicon glyphicon-ok"></i> 保存
					    </button>
				    </div>
				</form>
				</div>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>