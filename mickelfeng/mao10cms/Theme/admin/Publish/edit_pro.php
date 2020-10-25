<?php mc_template_part('header'); ?>
	<form role="form" method="post" action="<?php echo U('Home/perform/edit'); ?>">
	<div id="single-top">
		<div class="container-admin">
			<ol class="breadcrumb hidden-xs mb-20 mt-20" id="baobei-term-breadcrumb">
				<li>
					<a href="<?php echo U('home/index/index'); ?>">
						首页
					</a>
				</li>
				<li>
					<a href="<?php echo U('control/index/pro_index'); ?>">
						商品
					</a>
				</li>
				<li class="active">
					编辑商品
				</li>
				<div class="pull-right">
					<?php if(mc_is_admin()) : ?>
					<a href="<?php echo U('publish/index/index'); ?>" class="publish">发布商品</a>
					<?php endif; ?>
				</div>
			</ol>
			<div class="row">
				<div class="col-sm-6" id="pro-index-tl">
					<div id="pro-index-tlin">
					<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
						<script>
		function mc_fmimg_delete() {
			$('.item i').click(function(){
				var index = $(this).parent(".item").index()*1+1;
				$('#pub-imgadd .item:eq('+index +')').addClass('active');
				$(this).parent(".item").remove();
				$('.carousel-indicators li').last().remove();
			});
		};
		$(document).ready(function(){
			mc_fmimg_delete();
		});
		</script>
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
						        	//alert(this.result);
						        	var timestamp3 = new Date().getTime();
						            $('.item').removeClass('active');
									$('<div class="item active"><div class="imgshow"><img src="'+this.result+'"></div><i class="glyphicon glyphicon-remove-circle"></i><input id="mao10proimg-'+timestamp3+'" type="hidden" name="fmimg[]" value="'+this.result+'"></div>').prependTo('#pub-imgadd');
									var index = $('.carousel-indicators li').last().index()*1+1;
									$('<li data-target="#carousel-example-generic" data-slide-to="'+index+'"></li>').appendTo('.carousel-indicators');
									mc_fmimg_delete();
									$.ajax({
										type: 'POST',
										url: '<?php echo mc_site_url(); ?>/index.php?m=home&c=perform&a=publish_img',
										data:{src:this.result},
										success: function(data) {
											$('#mao10proimg-'+timestamp3).val(data);
										}
									});
						        } 
						} 
						</script>
						<ol class="carousel-indicators" id="publish-carousel-indicators"><?php $fmimg = mc_get_meta($_GET['id'],'fmimg',false); $fmimg = array_reverse($fmimg); foreach($fmimg as $val) : $num0++; ?><li data-target="#carousel-example-generic" data-slide-to="<?php echo $num0-1; ?>" class="<?php if($num0==1) echo 'active'; ?>"></li><?php endforeach; ?><li data-target="#carousel-example-generic" data-slide-to="<?php echo $num0; ?>"></li></ol>
						<div class="carousel-inner" id="pub-imgadd">
							<?php if($fmimg) : ?>
							<?php foreach($fmimg as $val) : ?>
							<?php $num++; ?>
							<div class="item <?php if($num==1) echo 'active'; ?>">
								<div class="imgshow"><img src="<?php echo $val; ?>"><input type="hidden" name="fmimg[]" value="<?php echo $val; ?>"></div>
								<i class="glyphicon glyphicon-remove-circle"></i>
							</div>
							<?php endforeach; ?>
							<div class="item">
								<div class="imgshow"><img src="<?php echo mc_theme_url(); ?>/img/upload.jpg"></div>
								<input type="file" id="picfile" onchange="readFile(this,1)" />
							</div>
							<?php else : ?>
							<div class="item active">
								<div class="imgshow"><img src="<?php echo mc_theme_url(); ?>/img/upload.jpg"></div>
								<input type="file" id="picfile" onchange="readFile(this,1)" />
							</div>
							<?php endif; ?>
						</div>
					</div>
					</div>
				</div>
				<div class="col-sm-6" id="pro-index-tr">
					<div id="pro-index-trin">
					<h1>
						<textarea name="title" class="form-control" placeholder="请填写商品标题"><?php echo mc_get_page_field($_GET['id'],'title'); ?></textarea>
					</h1>
					<?php $parameter = unserialize(mc_get_meta($_GET['id'],'parameter')); ?>
					<div class="h3s">
						<div class="row">
							<div class="col-xs-8 col">
								<label>现价</label>
								<div class="input-group">
									<input name="price" type="text" class="form-control" placeholder="0.00" value="<?php echo mc_get_meta($_GET['id'],'price'); ?>">
									<span class="input-group-addon">
										元
									</span>
								</div>
							</div>
							<div class="col-xs-4 col">
								<label>销量</label>
								<input name="xiaoliang" type="text" class="form-control ml-20" placeholder="0" value="<?php echo mc_get_meta($_GET['id'],'xiaoliang'); ?>">
							</div>
							<div class="canshu1" <?php if($parameter) : ?>style="display: none;"<?php else : ?><?php endif; ?>>
								<div class="col-xs-6 col mt-10">
									<label>库存</label>
									<input name="kucun" type="text" class="form-control" placeholder="没有参数时此项有效" value="<?php echo mc_get_meta($_GET['id'],'kucun'); ?>">
								</div>
								<div class="col-xs-6 col mt-10">
									<label>参数</label>
									<button type="button" class="btn btn-block btn-default">
										此商品有多种类型
									</button>
								</div>
							</div>
						</div>
					</div>
					<div class="canshu2" <?php if($parameter) : ?><?php else : ?>style="display: none;"<?php endif; ?>>
					<?php 
						$par_num_array = array(1,2,3,4,5,6,7,8,9,10);
						foreach($par_num_array as $par_num) :
					?>
					<div class="form-group pro-parameter pt-10" id="pro-parameter-<?php echo $par_num; ?>">
						<label>参数 - <?php echo $par_num; ?></label>
						<div class="row">
							<div class="col-sm-5">
								<input name="parameter[<?php echo $par_num; ?>][name]" type="text" class="form-control" placeholder="参数名称" value="<?php echo $parameter[$par_num]['name']; ?>">
							</div>
							<div class="col-sm-4">
								<input name="parameter[<?php echo $par_num; ?>][price]" type="text" class="form-control" placeholder="价格" value="<?php echo $parameter[$par_num]['price']; ?>">
							</div>
							<div class="col-sm-3">
								<input name="parameter[<?php echo $par_num; ?>][kucun]" type="text" class="form-control" placeholder="库存" value="<?php echo $parameter[$par_num]['kucun']; ?>">
							</div>
						</div>
					</div>
					<?php endforeach; ?>
					<button type="button" class="btn btn-default btn-block mt-10">此商品仅有一种类型</button>
					</div>
					<input type="hidden" id="canshu" name="canshu" value="<?php if(mc_get_meta($_GET['id'],'kucun')>0) : ?>0<?php else : ?>1<?php endif; ?>">
					<script>
						$('.canshu1 .btn').click(function(){
							$('.canshu1').fadeOut();
							$('.canshu2').fadeIn();
							$('#canshu').val(1);
						});
						$('.canshu2 .btn').click(function(){
							$('.canshu2').fadeOut();
							$('.canshu1').fadeIn();
							$('#canshu').val(0);
						});
					</script>
					<div class="h3s mt-20 mb-0">
						<div class="row">
							<div class="col-xs-4 col">
								<label>第三方购买</label>
								<input name="tb_name" type="text" class="form-control" value="<?php echo mc_get_meta($_GET['id'],'tb_name'); ?>" placeholder="名称">
							</div>
							<div class="col-xs-8 col">
								<label>链接</label>
								<input name="tb_url" type="text" class="form-control ml-20" value="<?php echo mc_get_meta($_GET['id'],'tb_url'); ?>" placeholder="http://">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>
							选择分类
						</label>
						<select class="form-control" name="term">
							<?php $terms = M('page')->where('type="term_pro"')->order('id desc')->select(); ?>
							<?php foreach($terms as $val) : ?>
							<option value="<?php echo $val['id']; ?>" <?php if(mc_get_meta($_GET['id'],'term')==$val['id']) echo 'selected'; ?>>
								<?php echo $val['title']; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container-admin" id="pro-single">
		<div class="row">
			<div class="col-sm-12" id="single">
				<div id="entry">
					<div class="form-group">
						<textarea name="content" class="form-control" rows="3"><?php echo mc_magic_out(mc_get_page_field($_GET['id'],'content')); ?></textarea>
					</div>
				</div>
				<div class="form-group">
					<input name="keywords" type="text" class="form-control" placeholder="关键词（Keywords），多个关键词以英文半角逗号隔开（选填）" value="<?php echo mc_get_meta($_GET['id'],'keywords'); ?>">
				</div>
				<div class="form-group">
					<textarea name="description" class="form-control" rows="3" placeholder="摘要（Description），会被搜索引擎抓取为网页描述（选填）"><?php echo mc_get_meta($_GET['id'],'description'); ?></textarea>
				</div>
				<button type="submit" class="btn btn-warning btn-block">
					<i class="glyphicon glyphicon-ok"></i> 提交
				</button>
			</div>
		</div>
	</div>
	<input name="id" type="hidden" value="<?php echo $_GET['id']; ?>">
	</form>
	<script charset="utf-8" src="<?php echo mc_site_url(); ?>/Kindeditor/kindeditor-all-min.js"></script>
				<script>
					var editor;
					KindEditor.ready(function(K) {
						editor = K.create('textarea[name="content"]', {
							resizeType : 1,
							allowPreviewEmoticons : false,
							allowImageUpload : true,
							height : 300,
							themeType : 'simple',
							langType : 'zh-CN',
							uploadJson : '<?php echo U('Publish/index/upload'); ?>',
							items : ['source', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'clearhtml', 'quickformat', 'selectall', '|', 
					'formatblock', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
					'italic', 'underline', 'strikethrough', 'removeformat', '|', 'image', 'multiimage', 'table', 'hr', 'emoticons', 'baidumap', 'link', 'unlink'],
							afterChange : function() {
								K(this).html(this.count('text'));
							}
						});
					});
				</script>
<?php mc_template_part('footer'); ?>