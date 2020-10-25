$(document).ready(function(){
    $("#btn-changepassword").click(changePassword);
    setBackground();
});

//设置背景
function setBackground(){
    var background = $("input[name=background]").val();
    $("body").css("background", "url(" + background + ")");
    $("body").css("background-size", "cover");
}

//修改密码
function changePassword(event){
    var tips = new Array();
    
    var oldPassword = $("input[name=old_password]").val();
    var newPassword = $("input[name=new_password]").val();
    var confirmPassword = $("input[name=confirm_password]").val();
    
    if(oldPassword == ""){
        tips.push("请填写旧密码");
    }
    if(newPassword.length < 6 || newPassword.length > 18){
        tips.push("密码必须在6-18位之间");
    }
    if(newPassword != confirmPassword){
        tips.push("两次输入的密码不一致");
    }
    
    if(tips.length > 0){
        showTips(tips);
        return;
    }
    
    //修改密码
    var command = new Command(
        "user_controller",
        "UserController",
        "execChangePassword",
        {old_password: oldPassword, new_password: newPassword}
    );
    command.send(msgHandler);
}