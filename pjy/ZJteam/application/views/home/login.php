<?php if(isset($_SESSION['zjb']) && isset($_SESSION['id'])){
	echo '<div style="z-index:1000;position:absolute;top:0;bottom:0;right:0;left:0;width:100%;height:100%;background-color:#fff;color:red;font-size:30px;text-align:center;">对不起，请不要重复登录</div>';
}?>	
<?php include 'application/views/home/header.php'?>	

<div class="main_btm"><!-- start main_btm -->
	<div class="container">
		<div class="main row" style="margin-top:40px;">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<center>
					<div class="contact-form center" style="padding:20px; border: 2px solid #eee;box-shadow: 10px 10px 5px #eee;border-radius: 4px;-webkit-border-radius: 4px;-moz-border-radius: 4px;-o-border-radius: 4px;">
							<span style="font-size:18px">登录</span>
							<hr style="height:1px;border:none;border-top:1px solid #eee;" />
							<form method="post" action="<?php echo site_url("home/checkLogin")?>">
								<div>
									<span><input placeholder="邮箱" name="email" type="email" class="form-control" id="email"></span>
								</div>
								</br>
								<div>
									<span><input placeholder="密码" name="password" type="password" class="form-control" id="email"></span>
								</div>
							    <div>
									<label class="fa-btn btn-1 btn-1e"><input type="submit" value="登录"></label>
							    </div>
							</form>
							<div style="float:right;font-size:12px;"><a href="<?php echo site_url('home/register');?>">注册为新用户</a></div>
					</div>
					</center>
				</div>
				<div class="col-md-4"></div>
		</div>
	</div>
</div>

<?php include 'application/views/home/footer.php'?>	