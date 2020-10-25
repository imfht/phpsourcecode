<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<div class="row">
			<form role="form" method="post" action="<?php echo U('home/perform/publish_term'); ?>">
			<div class="col-sm-9">
				<div class="form-group">
					<label>
						分类名称
					</label>
					<input name="title" type="text" class="form-control" placeholder="">
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>
						分类类型
					</label>
					<select class="form-control" name="type">
						<option value="pro" <?php if($_GET['type']=='pro') echo 'selected'; ?>>
							商品
						</option>
						<option value="baobei" <?php if($_GET['type']=='baobei') echo 'selected'; ?>>
							宝贝
						</option>
					</select>
				</div>
			</div>
			<div class="col-sm-12">
				<button type="submit" class="btn btn-warning btn-block">
					<i class="glyphicon glyphicon-ok"></i> 提交
				</button>
			</div>
			</form>
		</div>
	</div>
<?php mc_template_part('footer'); ?>