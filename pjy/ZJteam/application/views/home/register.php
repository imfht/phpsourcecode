<?php include 'application/views/home/header.php'?>	
<div class="main_btm"><!-- start main_btm -->
	<div class="container">
		<div class="main row" style="margin-top:40px;">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<center>
					<div class="login-content reg-content contact-form center" style="padding:20px; border: 2px solid #eee;box-shadow: 10px 10px 5px #eee;border-radius: 4px;-webkit-border-radius: 4px;-moz-border-radius: 4px;-o-border-radius: 4px;">
							<span style="font-size:18px">注册</span>
							<hr style="height:1px;border:none;border-top:1px solid #eee;" />
							<form style="padding:20px;font-size:16px;color:#333;" id="signupForm" method="post" action="<?php echo site_url("home/checkregister");?>">
								
								<div class="login-error"></div>
								<div class="row">
									<input placeholder="注册邮箱" type="text" value="<?php echo set_value("email");?>" class="input-text-user noPic input-click form-control" name="email" id="email">
									<span style="color:red;"><?php echo form_error('email'); ?></span>
								</div>
								</br>
								<div class="row">
									<input placeholder="密码" class="form-control" type="password" value="" class="input-text-password noPic input-click" name="password" id="password">
									<span style="color:red;"><?php echo form_error('password'); ?></span>
								</div>
								</br>
								<div class="row">
									<input placeholder="确认密码" class="form-control" type="password" value="" class="input-text-password noPic input-click" name="password2" id="password2">
									<span style="color:red;"><?php echo form_error('password2'); ?></span>
								</div>
								</br>
								<div class="row">
									<input placeholder="姓名" type="text" value="<?php echo set_value("truename");?>" class="form-control input-text-user noPic input-click" name="truename" id="truename">
									<span style="color:red;"><?php echo form_error('truename'); ?></span>
								</div>
								</br>
								<div class="row">
									<input placeholder="学校" type="text" value="<?php echo set_value("school");?>" class="form-control input-text-user noPic input-click" name="school" id="school">
									<span style="color:red;"><?php echo form_error('school'); ?></span>
								</div>
								</br>
								<div class="row">
									<input placeholder="手机" type="text" value="<?php echo set_value("phone");?>" class="form-control input-text-user noPic input-click" name="phone" id="phone">
									<span style="color:red;"><?php echo form_error('phone'); ?></span>
								</div>
								</br>
								<div class="row">
									<input placeholder="QQ" type="text" value="<?php echo set_value("qq");?>" class="form-control input-text-user noPic input-click" name="qq" id="qq">
									<span style="color:red;"><?php echo form_error('qq'); ?></span>
								</div>
								</br>
								<input id="man" type="radio" checked="checked" value="男" name="sex" />男&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input id="woman" type="radio" value="女"  name="sex"/>女
								</br>
								</br>
							    <div>
									<label class="fa-btn btn-1 btn-1e"><input type="submit" value="注册"></label>
							    </div>
							</form>
							<div style="float:right;font-size:12px;"><a href="<?php echo site_url('home/logindex');?>">已有账号直接登录</a></div>
					</div>
					</center>
				</div>
				<div class="col-md-4"></div>
		</div>
	</div>
</div>

<?php include 'application/views/home/footer.php'?>	