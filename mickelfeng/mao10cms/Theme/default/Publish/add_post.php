<?php mc_template_part('header'); ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 home-main">
				<ol class="breadcrumb mb-20 mt-40">
					<li>
						<a href="<?php echo U('home/index/index'); ?>">
							首页
						</a>
					</li>
					<li>
						<a href="<?php echo U('post/group/index'); ?>">
							社区
						</a>
					</li>
					<li class="active">
						新建主题
					</li>
				</ol>
				<form role="form" method="post" action="<?php echo U('Home/perform/publish'); ?>">
					<div class="row">
						<div class="col-sm-4 col-lg-3">
							<div class="form-group">
								<label>
									版块
								</label>
								<select class="form-control" name="group">
								<?php $group = M('page')->where('type="group"')->order('date desc')->select(); if($group) : foreach($group as $val) : ?>
									<option value="<?php echo $val['id']; ?>" <?php if($_GET['group']==$val['id']) echo 'selected'; ?>><?php echo $val['title']; ?></option>
								<?php endforeach; endif; ?>
								</select>
							</div>
						</div>
						<div class="col-sm-8 col-lg-9">
							<div class="form-group">
								<label>
									标题
								</label>
								<input name="title" type="text" class="form-control" placeholder="" value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>
							主题内容
						</label>
						<textarea name="content" class="form-control" rows="3"></textarea>
					</div>
					<button type="submit" class="btn btn-warning btn-block">
						保存
					</button>
				</form>
			</div>
		</div>
	</div>
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