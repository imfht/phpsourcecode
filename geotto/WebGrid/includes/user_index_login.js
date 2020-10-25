$(document).ready(function(){
	$("#btn-login").click(login);	
});

//打开登录对话框
function openLoginDialog(event){
	$("#dialog-login").toggle();
}

//登录
function login(event){
	var tips = new Array();
		//获取参数
		var name = $("input[name=name]").val();
		var password = $("input[name=password]").val();
		var keep_signed = document.getElementById("chk-keep").checked?"yes":"no";
		
		if(trim(name) == ""){
				tips.push("请输入用户名");
		}
		if(trim(password) == ""){
			tips.push("请输入密码");
		}
		
		if(tips.length > 0){
			showTips(tips);
			return;
		}
		
		var command = new Command(
			"user_controller",
			"UserController",
			"doLogin",
			{name: name, password: password, keep_signed: keep_signed}
		);
		command.send(loginHandler);
}

//处理登录返回信息
function loginHandler(msg){	
		if(msg.no == msg.MSG_SUCCESS){
			setTimeout("window.location.reload();", 1000);
		}
		
		showTips([msg.content]);
}
