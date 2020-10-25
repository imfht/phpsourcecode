<script type="text/javascript">
<!--
$(function(){
	$("#old_password").blur(function(){
		valiOldPassword($(this).val());
	});

	$("#new_password").blur(function(){
		valiNewPassword($(this).val());
	});

	$("#again_password").blur(function(){
		valiAgainPassword($(this).val());
	});
})
/**
 * 验证用户原始密码是否正确
 */
function valiOldPassword($password)
{
	var flag="";
	if($password==""){
		$("#old_span").removeClass().addClass("errorMessage").text('原始密码不能为空');
		return false;
	}else{
		$.ajax({
			url:'<?php echo Yii::app()->baseUrl;?>/member/ajax',
			data:{password:$password},
			type:'POST',
			async:false,
			success:function(data){
				if(data=='passwordTrue'){
					$("#old_span").removeClass().addClass("successMessage").text('原始密码正确');
					flag = true;
				}else{
					$("#old_span").removeClass().addClass("errorMessage").text('原始密码错误');
					flag = false;
				}
			},
		});
	}
	return flag;
}
/**
 * 验证新密码是否正确
 */
function valiNewPassword($password)
{
	if($password==""){
		$("#new_span").removeClass().addClass("errorMessage").text('新密码不能为空');
		return false;
	}
	if($password==$("#old_password").val()){
		$("#new_span").removeClass().addClass("errorMessage").text('新旧密码不能一致');
		return false;
	}
	var Re = /\s/;
	if(Re.test($password)){
		$("#new_span").removeClass().addClass("errorMessage").text('密码中不能包涵空格');
		return false;
	}
	var Re = /^[-`=\\\[\];',\.~\/!@#$%^&*()_+|{}:"<>?0-9a-zA-Z]{6,15}$/; 
	if(!Re.test($password)){
		$("#new_span").removeClass().addClass("errorMessage").text('密码必须大于6位小于15位');
		return false;
	}
	$("#new_span").removeClass().addClass("successMessage").text('密码输入正确');
	return true;
}
/**
 * 验证二次确认密码是否一致
 */
function valiAgainPassword($password)
{
	if($password==""){
		$("#again_span").removeClass().addClass("errorMessage").text('确认密码不能为空');
		return false;
	}
	if($password!=$("#new_password").val()){
		$("#again_span").removeClass().addClass("errorMessage").text('二次输入密码不一致');
		return false;
	}
	$("#again_span").removeClass().addClass("successMessage").text('二次输入密码正确');
	return true;
}
function change()
{
	var v1 = valiOldPassword($("#old_password").val());
	var v2 = valiNewPassword($("#new_password").val());
	var v3 = valiAgainPassword($("#again_password").val());
	if(v1==false)
		return false;
	if(v2==false)
		return false;
	if(v3==false)
		return false;
	return true;
}
//-->
</script>
<div id="main_right">
  <div class="email password_email"> 

  <h2>修改密码</h2>
  <p>填写您要密码</p>
   <form action="<?php echo Yii::app()->request->baseUrl;?>/member/updatePassword" id="password-form" method="post" onsubmit="return change()">
  <p style="color:#999999;">
 	 <label for="old_password">原始密码：</label>
 	 <input type="password" id="old_password" name="Password[old_password]" />
 	 <span id="old_span"></span>
  </p>
  <p style="color:#999999;">
      <label for="new_password">新的密码：</label>
      <input type="password" id="new_password" name="Password[new_password]" />
      <span id="new_span"></span>
  </p>
  <p style="color:#999999;">
     <label for="again_password">确认密码：</label>
     <input type="password" id="again_password" name="Password[again_password]" />
     <span id="again_span"></span>
   </p>
      <p style="padding-left:60px;"><input type="submit" value="提交"  style="margin-right:20px;"/><input type="reset" value="重置" /></p>
  </form>
</div>
</div>
  


