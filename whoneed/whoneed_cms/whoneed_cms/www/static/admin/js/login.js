function checkspace(checkstr) {
	var str = '';
	for(i = 0; i < checkstr.length; i++) {
		str = str + ' ';
	}
	return (str == checkstr);
}
//登陆检测
function checkLoginForm(){
	var frm = document.form1
	if(frm.User.value == ""){
		alert('用户名不能为空！');
		frm.User.focus();
		return false;
	}
	if(frm.Pass.value == ""){
		alert('密码不能为空！');
		frm.Pass.focus();
		return false;
	}
	if(frm.authCode.value == ""){
		alert('验证码不能为空！');
		frm.authCode.focus();
		return false;
	}
	frm.submit();
}
