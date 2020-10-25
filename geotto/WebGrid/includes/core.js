//定义常量
var SUCCESS = 0;
var NOT_LOGGED = -1;
var NOT_PERMITTED = -2;
var ARG_ERROR = -3;
var ALREADY_EXISTS = -4;
var ERROR = -5;

var SEP_I = "[sep1]";	//记录分隔符
var SEP_II = "[sep2]";	//字段分隔符

var ICON_PATH = "storage/icons";	//图标存储地址

$(document).ready(function(){
	//设置提示框位置
    var winW = $(window).width();
    var tipsW = $('#tips').width();
    var tipsX = (winW-tipsW)/2;
    $('#tips').css('left',tipsX);

    //设定计时器
    var counter = $("#time-counter");
    if(typeof(counter) != "undefined"){
	var href = $("#jump").attr("href");
	timing(counter, href);
    }
    
    //设定关闭窗口按钮
    $(".closeit").click(function(){
		$(this).parents(".dialog").hide();
	});
    
    //设置默认图标
    setDefaultIcon();
});

//显示提示框
function showTips(tips){
	var content = '';//提示框内容
	for(var i=0;i<tips.length;i++){
		content += "<div class=\"tip\">" + tips[i] + "</div>";
	}
	
	$('#tips').html(content);
	$('#tips').show();
	
	setTimeout("$('#tips').hide();",5000);
}

//删除字符串两侧空格
function trim(str){ //删除左右两端的空格
	　　return str.replace(/(^\s*)|(\s*$)/g, "");
}

//服务器-浏览器通信对象
function Message(strMsg){
    eval("message=(" + strMsg + ")");
    
    message.MSG_SUCCESS = 0;
    message.MSG_ERROR = -1;
    message.MSG_NOT_LOGGED = -2;
    message.MSG_NOT_PERMITTED = -3;
    message.MSG_ARG_ERROR = -4;
    message.MSG_ALREADY_EXISTS = -5;
    message.MSG_NONE = -6;
    
    return message;
}

//浏览器-服务器通信对象
function Command(page, target, func, args){
	var command = new Object;
    
    command.page = page;
    command.target = target;
    command.func = func;
    command.args = args;
    
    //发送命令
    command.send = function(handler){
    	var dest = "index.php?file=" + this.page + "&class=" + this.target + "&fun=" + this.func;
        $.post(dest, args, function(data, stauts){
        	var msg = Message(data);
            handler(msg);
        });
    }
    
    return command;
}

//处理服务器返回数据的默认操作
function msgHandler(msg){
    var tips = new Array();
    tips.push(msg.content);
    showTips(tips);
    
    if(msg.no == msg.MSG_SUCCESS){
    	setTimeout("window.location.reload();", 1000);
    }
}

//将元素加入到数组中，该元素不能在数组中出现
function array_insert(array, elem){
	//查看元素是否在数组中
    var index = array_index(array, elem);
    if(index != -1)
        return false;
    
    //插入元素
    array.push(elem);
    return true;
}

//检索元素在数组中的位置
function array_index(array, elem){
    for(var i=0; i<array.length; i++){
    	if(array[i] == elem)
            return i;
    }
    
    return -1;
}

//从数组中删除元素
function array_delete(array, elem){
	var index = array_index(array, elem);
    if(index != -1){
    	array.splice(index, 1);
        return true;
    }
    
    return false;
}

//计时器
function timing(counter, href){
    var count = parseInt(counter.html());
    count--;
    if(count <= 0){
	window.location.href = href;
    }
    else{
	counter.html(count);
	setTimeout(timing, 1000, counter, href);
    }
}

//禁用按钮
function disableBtn(btn){
    btn.css("background-color", "#BFBFBF");
    btn.css("border", "1px solid #4D4D4D");
}

//启用按钮
function enableBtn(btn){
    btn.css("background-color", "");
    btn.css("border", "");
}

//设置默认图标
function setDefaultIcon(){
    $(".grid>img").error(function(){
        $(this).attr("src", ICON_PATH + "/default.png");
    });
}

//添加cookie
function addCookie(name,val,expires){
	var str = name + "=" + escape(val);
	if(expiredHours > 0){
	var date = new Date();
	date.setTime(date.getTime() + expires);
	str += "; expires=" + date.toGMTString();
	}
	document.cookie = str;
}

//获取cookie
function getCookie(name){
	var cookies = document.cookie.split("; ");
	for(var i = 0;i < cookies.length;i ++){
	var temp = cookies[i].split("=");
	if(temp[0] == name)
		return unescape(temp[1]);
	}
	
	return "";
}

//删除cookie
function delCookie(name){
	var date = new Date();
	date.setTime(date.getTime() - 10000);
	document.cookie = name + "=a; expires=" + date.toGMTString();
}
