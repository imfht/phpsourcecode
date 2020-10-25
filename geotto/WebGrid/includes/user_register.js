$(document).ready(function(){
	//添加事件监听器
	$("#register").click(register);
});

//注册
function register(event){
	var tips = new Array();
	
	//检验各字段
	var name = $("input[name=name]").val();
	var password = $("input[name=password]").val();
	var confirmPassword = $("input[name=confirm_password]").val();
	
	name = trim(name);
	password = trim(password);
	confirmPassword = trim(confirmPassword);
	
	if(name == ""){
		tips.push("请填写用户名");
	}
	if(password.length < 6){
		tips.push("密码的长度不能小于6位");
	}
	if(confirmPassword != password){
		tips.push("两次输入的密码不一致");
	}
	
	if(tips.length > 0){
		showTips(tips);
		return;
	}
	
	//注册
	var command = Command('user_controller', 'UserController', 'doRegister', {
		name: name,
		password: password
	});
	command.send(msgHandler);
}
