<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<div class="row">
			<div class="col-lg-12">
				<div id="single">
				<form role="form" method="post" action="<?php echo mc_page_url(); ?>">
				    <div class="form-group">
				        <label>
				            网站名称
				        </label>
				        <input name="site_name" type="text" class="form-control" value="<?php echo mc_option('site_name'); ?>" placeholder="">
				        <p class="help-block">
				            网站的名字，会显示在title和主题的某些部分
				        </p>
				    </div>
				    <div class="form-group">
				        <label>
				            网站地址
				        </label>
				        <input name="site_url" type="url" class="form-control" value="<?php echo mc_option('site_url'); ?>" placeholder="">
				        <p class="help-block">
				            访问网站的地址，请勿删除<code>http://</code>，最后请勿加<code>/</code>
				        </p>
				    </div>
				    <div class="form-group">
				        <label>
				            主题
				        </label>
				        <div class="row">
				        	<div class="col-md-4 col-lg-3">
						        <select name="theme" class="form-control">
						        <?php $dir = THINK_PATH."../Theme"; if (is_dir($dir)) : if ($dh = opendir($dir)) : while (($file = readdir($dh))!= false) : $filePath = $dir.'/'.$file; if (is_dir($filePath)) : if($file!='.' && $file!='..' && $file!='admin' && $file!='mobile') : ?>
						    	<option value="<?php echo $file; ?>" <?php if(mc_option('theme')==$file) : ?>selected<?php endif; ?>><?php echo $file; ?></option>
						    	<?php endif;endif; endwhile; closedir($dh); endif; endif; ?>
						        </select>
				        	</div>
				        </div>
				        <p class="help-block">
				            网站使用的主题，默认为<code>default</code>
				        </p>
				    </div>
				    
				    <div class="form-group">
				        <label>
				            网站主色调
				        </label>
				        <div class="row">
				        	<div class="col-sm-3 col-md-2">
								<input name="site_color" type="color" class="form-control" value="<?php if(mc_option('site_color')) : echo mc_option('site_color'); else : echo '#ff4800'; endif; ?>" placeholder="">
				        	</div>
				        </div>
				        <p class="help-block">
				            选色器仅支持Chrome、Safari、Opera等较新版本浏览器，其他浏览器请手动填写颜色代码，如：#ff4a00。
				        </p>
				    </div>
				    <div class="form-group">
						<label>
							LOGO设置
						</label>
						<div class="row">
							<div class="col-sm-6" id="pub-logoadd">
								<img class="default-img" src="<?php if(mc_option('logo')) : echo mc_option('logo'); else : ?><?php echo mc_theme_url(); ?>/img/logo-xs.png<?php endif; ?>">
								<input type="hidden" name="logo" id="pub-input2" value="<?php if(mc_option('logo')) : echo mc_option('logo'); else : ?><?php echo mc_theme_url(); ?>/img/logo-xs.png<?php endif; ?>">
								<input type="file" id="picfile" onchange="readFile(this,2)" />
							</div>
						</div>
				        <p class="help-block">
				            建议LOGO高度不超过 70px
				        </p>
					</div>
				    <div class="form-group">
						<label>
							默认用户背景图片
						</label>
						<div class="row">
							<div class="col-sm-6" id="pub-imgadd">
								<img class="default-img" src="<?php if(mc_option('fmimg')) : echo mc_option('fmimg'); else : ?><?php echo mc_theme_url(); ?>/img/user_bg.jpg<?php endif; ?>">
								<input type="hidden" name="fmimg" id="pub-input" value="<?php if(mc_option('fmimg')) : echo mc_option('fmimg'); else : ?><?php echo mc_theme_url(); ?>/img/user_bg.jpg<?php endif; ?>">
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
				        	if(id==1) {
					        	$('#pub-imgadd img').attr('src',this.result);
					        	$('#pub-imgadd #pub-input').val(this.result);
				        	} else if(id==2) {
					        	$('#pub-logoadd img').attr('src',this.result);
								$('#pub-logoadd #pub-input2').val(this.result);
				        	} else if(id==3) {
					        	$('#pub-imgadd-1 .default-img').attr('src',this.result);
								$('#pub-imgadd-1 input.mb-101').val(this.result);
							} else if(id==4) {
					        	$('#pub-imgadd-2 .default-img').attr('src',this.result);
								$('#pub-imgadd-2 input.mb-101').val(this.result);
							} else if(id==5) {
					        	$('#pub-imgadd-3 .default-img').attr('src',this.result);
								$('#pub-imgadd-3 input.mb-101').val(this.result);
							}
				            //alert(this.result);
				        } 
				} 
				</script>
						</div>
					</div>
				    <div class="form-group">
				        <label>
				            每页文章数量
				        </label>
				        <input name="page_size" type="number" min="1" class="form-control" value="<?php echo mc_option('page_size'); ?>" placeholder="">
				        <p class="help-block">
				            请设置大于1的整数
				        </p>
				    </div>
				    <div class="form-group">
				        <label>
				            评论审核
				        </label>
				        <div class="clearfix"></div>
				        <label class="radio-inline">
							<input type="radio" name="shenhe_comment" value="1" <?php if(mc_option('shenhe_comment')!=2) : ?>checked<?php endif; ?>>
							无须审核
						</label>
				        <label class="radio-inline">
							<input type="radio" name="shenhe_comment" value="1" <?php if(mc_option('shenhe_comment')==2) : ?>checked<?php endif; ?> disabled>
							需要审核
						</label>
				    </div>
				    <div class="form-group">
						<label>
							首页幻灯设置
						</label>
						<div class="row">
							<div class="col-sm-4" id="pub-imgadd-1">
								<div class="pub-imgadd">
								<?php if(mc_option('homehdimg1')) : ?>
								<img class="default-img mb-10" src="<?php echo mc_option('homehdimg1'); ?>">
								<?php else : ?>
								<img class="default-img mb-10" src="<?php echo mc_theme_url(); ?>/img/upload.jpg">
								<?php endif; ?>
								<input type="file" id="picfile" onchange="readFile(this,3)" />
								</div>
								<input type="text" class="form-control mb-10 mb-101" name="homehdimg1" value="<?php echo mc_option('homehdimg1'); ?>" placeholder="幻灯图片地址">
								<input type="text" class="form-control mb-10" name="homehdtitle1" placeholder="幻灯标题" value="<?php echo mc_option('homehdtitle1'); ?>">
								<textarea class="form-control mb-10" rows="3" name="homehdtext1" placeholder="幻灯描述，文字不宜过长"><?php echo mc_option('homehdtext1'); ?></textarea>
								<input type="text" class="form-control mb-10" name="homehdbtn1" placeholder="按钮文字" value="<?php echo mc_option('homehdbtn1'); ?>">
								<input type="url" class="form-control" name="homehdlnk1" placeholder="按钮链接" value="<?php echo mc_option('homehdlnk1'); ?>">
							</div>
							<div class="col-sm-4" id="pub-imgadd-2">
								<div class="pub-imgadd">
								<?php if(mc_option('homehdimg2')) : ?>
								<img class="default-img mb-10" src="<?php echo mc_option('homehdimg2'); ?>">
								<?php else : ?>
								<img class="default-img mb-10" src="<?php echo mc_theme_url(); ?>/img/upload.jpg">
								<?php endif; ?>
								<input type="file" id="picfile" onchange="readFile(this,4)" />
								</div>
								<input type="text" class="form-control mb-10 mb-101" name="homehdimg2" value="<?php echo mc_option('homehdimg2'); ?>" placeholder="幻灯图片地址">
								<input type="text" class="form-control mb-10" name="homehdtitle2" placeholder="幻灯标题" value="<?php echo mc_option('homehdtitle2'); ?>">
								<textarea class="form-control mb-10" rows="3" name="homehdtext2" placeholder="幻灯描述，文字不宜过长"><?php echo mc_option('homehdtext2'); ?></textarea>
								<input type="text" class="form-control mb-10" name="homehdbtn2" placeholder="按钮文字" value="<?php echo mc_option('homehdbtn2'); ?>">
								<input type="url" class="form-control" name="homehdlnk2" placeholder="按钮链接" value="<?php echo mc_option('homehdlnk2'); ?>">
							</div>
							<div class="col-sm-4" id="pub-imgadd-3">
								<div class="pub-imgadd">
								<?php if(mc_option('homehdimg3')) : ?>
								<img class="default-img mb-10" src="<?php echo mc_option('homehdimg3'); ?>">
								<?php else : ?>
								<img class="default-img mb-10" src="<?php echo mc_theme_url(); ?>/img/upload.jpg">
								<?php endif; ?>
								<input type="file" id="picfile" onchange="readFile(this,5)" />
								</div>
								<input type="text" class="form-control mb-10 mb-101" name="homehdimg3" value="<?php echo mc_option('homehdimg3'); ?>" placeholder="幻灯图片地址">
								<input type="text" class="form-control mb-10" name="homehdtitle3" placeholder="幻灯标题" value="<?php echo mc_option('homehdtitle3'); ?>">
								<textarea class="form-control mb-10" rows="3" name="homehdtext3" placeholder="幻灯描述，文字不宜过长"><?php echo mc_option('homehdtext3'); ?></textarea>
								<input type="text" class="form-control mb-10" name="homehdbtn3" placeholder="按钮文字" value="<?php echo mc_option('homehdbtn3'); ?>">
								<input type="url" class="form-control" name="homehdlnk3" placeholder="按钮链接" value="<?php echo mc_option('homehdlnk3'); ?>">
							</div>
						</div>
					</div>
				    <div class="form-group">
				        <label>
				            模块名称设置
				        </label>
				        <input name="pro_name" type="text" class="form-control" placeholder="商品" value="<?php echo mc_option('pro_name'); ?>">
				    </div>
				    <div class="form-group">
				        <input name="group_name" type="text" class="form-control" placeholder="社区" value="<?php echo mc_option('group_name'); ?>">
				    </div>
				    <div class="form-group">
				        <input name="article_name" type="text" class="form-control" placeholder="新闻" value="<?php echo mc_option('article_name'); ?>">
				    </div>
				    <div class="form-group">
				        <label>
				            邮件SMTP设置
				        </label>
				        <input name="stmp_from" type="text" class="form-control" value="<?php echo mc_option('stmp_from'); ?>" placeholder="发送邮件账号">
				    </div>
				    <div class="form-group">
				        <input name="stmp_name" type="text" class="form-control" value="<?php echo mc_option('stmp_name'); ?>" placeholder="发件人名字">
				    </div>
				    <div class="form-group">
				        <input name="stmp_host" type="text" class="form-control" value="<?php echo mc_option('stmp_host'); ?>" placeholder="SMTP服务器">
				    </div>
				    <div class="form-group">
				        <input name="stmp_port" type="text" class="form-control" value="<?php echo mc_option('stmp_port'); ?>" placeholder="SMTP服务器端口">
				    </div>
				    <div class="form-group">
				        <input name="stmp_username" type="text" class="form-control" value="<?php echo mc_option('stmp_username'); ?>" placeholder="SMTP服务用户名">
				    </div>
				    <div class="form-group">
				        <input name="stmp_password" type="text" class="form-control password" value="<?php echo mc_option('stmp_password'); ?>" placeholder="SMTP服务密码">
				        <p class="help-block">
				            设置STMP后，找回密码功能才可正常使用。如果不会设置，请前往Mao10CMS官网咨询。
				        </p>
				    </div>
				    <div class="form-group">
				        <label>
				            又拍云接口配置
				        </label>
				        <div class="clearfix"></div>
				        <label class="radio-inline">
							<input type="radio" name="upyun" value="1" <?php if(mc_option('upyun')!=2) : ?>checked<?php endif; ?>>
							关闭
						</label>
				        <label class="radio-inline">
							<input type="radio" name="upyun" value="2" <?php if(mc_option('upyun')==2) : ?>checked<?php endif; ?>>
							开启
						</label>
						<div class="clearfix"></div>
				    </div>
				    <div class="form-group">
				        <input name="upyun_url" type="text" class="form-control" value="<?php echo mc_option('upyun_url'); ?>" placeholder="外链地址">
				    </div>
				    <div class="form-group">
				        <input name="upyun_bucket" type="text" class="form-control" value="<?php echo mc_option('upyun_bucket'); ?>" placeholder="空间名">
				    </div>
				    <div class="form-group">
				        <input name="upyun_user" type="text" class="form-control" value="<?php echo mc_option('upyun_user'); ?>" placeholder="操作员账号">
				    </div>
				    <div class="form-group">
				        <input name="upyun_pwd" type="text" class="form-control" value="<?php echo mc_option('upyun_pwd'); ?>" placeholder="密码">
						<p class="help-block">
				            将新上传的文件托管于又拍云。
				        </p>
				    </div>
				    <div class="form-group">
				        <label>
				            QQ快速登陆
				        </label>
				        <div class="clearfix"></div>
				        <label class="radio-inline">
							<input type="radio" name="loginqq" value="1" <?php if(mc_option('loginqq')!=2) : ?>checked<?php endif; ?>>
							关闭
						</label>
				        <label class="radio-inline">
							<input type="radio" name="loginqq" value="2" <?php if(mc_option('loginqq')==2) : ?>checked<?php endif; ?>>
							开启
						</label>
						<div class="clearfix"></div>
				    </div>
				    <div class="form-group">
				        <input name="loginqq_appid" type="text" class="form-control" value="<?php echo mc_option('loginqq_appid'); ?>" placeholder="App ID">
				    </div>
				    <div class="form-group">
				        <input name="loginqq_appkey" type="text" class="form-control" value="<?php echo mc_option('loginqq_appkey'); ?>" placeholder="App Key">
				        <p class="help-block">请确认文件<code>connect-qq/API/comm/inc.php</code>可写，回调地址：<code><?php echo mc_option('site_url'); ?>/connect-qq</code></p>
				    </div>
				    <div class="form-group">
				        <label>
				            新浪微博快速登陆
				        </label>
				        <div class="clearfix"></div>
				        <label class="radio-inline">
							<input type="radio" name="loginweibo" value="1" <?php if(mc_option('loginweibo')!=2) : ?>checked<?php endif; ?>>
							关闭
						</label>
				        <label class="radio-inline">
							<input type="radio" name="loginweibo" value="2" <?php if(mc_option('loginweibo')==2) : ?>checked<?php endif; ?>>
							开启
						</label>
						<div class="clearfix"></div>
				    </div>
				    <div class="form-group">
				        <input name="loginweibo_appid" type="text" class="form-control" value="<?php echo mc_option('loginweibo_appid'); ?>" placeholder="App Key">
				    </div>
				    <div class="form-group">
				        <input name="loginweibo_appkey" type="text" class="form-control" value="<?php echo mc_option('loginweibo_appkey'); ?>" placeholder="App Secret">
				        <p class="help-block">请确认文件<code>connect-weibo/config.php</code>可写</p>
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