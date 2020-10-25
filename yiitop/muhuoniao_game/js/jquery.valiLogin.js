function change()
{
	return vali();
}
function vali()
{
	var flag;
	$.ajax({
		url:'/site/ajaxLogin',
		type:'POST',
		async:false,
		data:{password:$("#LoginForm_password").val(),username:$("#LoginForm_username").val(),code:$("#LoginForm_verifyCode").val()},
		success:function(data){
			switch(data){
				case 'namenull':
					flag = false;alert('用户名错误');break;
				case 'passworderror':
					flag = false;alert('密码错误');break;
				case 'verifyCodeerror':
					flag = false;alert('验证码错误');break;
				default:
					flag = true;
			}
		},
	});
	return flag;
}