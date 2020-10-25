<?php include 'application/views/home/header.php'?>	
<div class="social_network_likes main_bg container" style="margin-top:30px;">
	<ul class="list-unstyled">
		<li><a href="<?php echo site_url('home/myzoom');?> " class="facebook-followers"><div class="followers"><p><span>我的活动</span></p></div></a></li>
		<li><a href="<?php echo site_url('home/modInfo');?>" class="dribble"><div class="followers"><p><span>修改资料</span></p></div></a></li>
		<div class="clear"> </div>
	</ul>
</div>
<div class="main_btm"><!-- start main_btm -->
	<div class="container">
		<div class="main row" style="margin-top:40px;">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<center>
						<div class="login-content reg-content contact-form center" style="padding:20px; border: 2px solid #eee;box-shadow: 10px 10px 5px #eee;border-radius: 4px;-webkit-border-radius: 4px;-moz-border-radius: 4px;-o-border-radius: 4px;">
							<span style="font-size:18px">修改资料</span>
							<hr style="height:1px;border:none;border-top:1px solid #eee;" />
							<form style="padding:20px;font-size:16px;color:#333;" id="signupForm" method="post" action="<?php echo site_url("home/modUserInfo");?>">
								<div class="row">
									<input placeholder="姓名" type="text" value="<?php echo $userinfo->truename;?>" class="form-control input-text-user noPic input-click" name="truename" id="truename">
									<span style="color:red;"><?php echo form_error('truename'); ?></span>
								</div>
								</br>
								<div class="row">
									<input placeholder="学校" type="text" value="<?php echo $userinfo->school;?>" class="form-control input-text-user noPic input-click" name="school" id="school">
									<span style="color:red;"><?php echo form_error('school'); ?></span>
								</div>
								</br>
								<div class="row">
									<input placeholder="手机" type="text" value="<?php echo $userinfo->phone;?>" class="form-control input-text-user noPic input-click" name="phone" id="phone">
									<span style="color:red;"><?php echo form_error('phone'); ?></span>
								</div>
								</br>
								<div class="row">
									<input placeholder="QQ" type="text" value="<?php echo $userinfo->qq;?>" class="form-control input-text-user noPic input-click" name="qq" id="qq">
									<span style="color:red;"><?php echo form_error('qq'); ?></span>
								</div>
								</br>
								<input id="man" type="radio" <?php if($userinfo->sex=="女"){echo 'checked="checked"';}else{echo "";}?> value="男" name="sex" />男&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input id="woman" type="radio" value="女" <?php if($userinfo->sex=="女"){echo 'checked="checked"';}else{echo "";}?>  name="sex"/>女
								</br>
								</br>
							    <div>
									<label class="fa-btn btn-1 btn-1e"><input type="submit" value="修改"></label>
							    </div>
							</form>
					</div>
					</center>
				</div>
				<div class="col-md-4"></div>
		</div>
	</div>
</div>

<?php include 'application/views/home/footer.php'?>	