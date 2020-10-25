<script>
$(function(){
	$("#now_validate").bind("click",function(){
		$(".email").empty().removeClass("errorMessage").addClass("successMessage").text("正在发送邮件......请稍等！").show(0);
		$.post("/member/emailValidateAjax",{},
			function(data){
				if(data==1){
					$(".email").empty().removeClass("errorMessage").addClass("successMessage").text("邮件已经发送成功，请登录邮箱确认邮件").show(0);
					$(".email").append("<a href='/member/email'>点此返回</a>");
				}else{
					$(".email").empty().removeClass("successMessage").addClass("errorMessage").text("邮件发送失败请重新发送").show(0);
					$(".email").append("<a href='/member/email'>点此返回</a>");
				}	
		})
	})
	
})
</script>
<div id="main_right">
	<div class="email"> 
		<h2>邮箱注册成功</h2>
		<?php if ($model->email_validate == 1): ?>
	    	<p>您的邮箱已认证成功！</p>
 			<p style="color:#999999;">您的邮箱为：<?php echo substr_replace($model->email,'xxxxxxx',2,-8);?></p>
 		<?php endif; ?>
	    
		<?php if ($model->email_validate == 0): ?>
			<p>你注册的邮箱为：<?php echo $model->email;?>m</p>
			<p><a href="javascript:void(0)" id="now_validate">立即认证</a>  <a href="/member/updateEmail">修改邮箱</a></p>
		<?php endif; ?>
		<div id="notice" style="display: none;"></div>
	</div>
</div>