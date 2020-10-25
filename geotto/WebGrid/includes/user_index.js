var USER_URL = "index.php?file=user_controller&class=UserController";
var WIDGETLIST_URL = USER_URL + "&fun=widgetlist";
var MYWIDGETS_URL = USER_URL + "&fun=mywidgets";

var selected_sites;	//选中的网址
var desktops;    //用户桌面
var currentDesktop = -1;	//当前桌面
var scrolling = false;	//是否正在滚动
var selectedWidget = -1;	//选中的控件

$(document).ready(function(){
	//自动登录
	autoSigned();
	
	var winH = $(window).height();
	var winW = $(window).width();
	
	//设置菜单栏高度
	$(".menu").css("height", winH);
	$("#widget-container").css("height", winH);
	
	//设置窗口位置
	$(".dialog").each(function(){
		var dialogW = $(this).width();
		var dialogH = $(this).height();
		var posX = Math.floor((winW - dialogW)/2);
		var posY = Math.floor((winH - dialogH)/2);
		$(this).css("left", posX);
		$(this).css("top", posY);
	});

    //设置当前的桌面
    var strDesktops = $("input[name=str_desktops]").val();
    if(strDesktops != "none"){
        desktops = strDesktops.split(",");
        currentDesktop = desktops[0];
    }else{
        desktops = ['-1'];
    }
    reloadDesktop(desktops[0]);
    reloadMyWidgets();
    
    //设置桌面背景
    var background = $("input[name=background]").val();
    $("body").css("background", "url(" + background + ")");
    $("body").css("background-size", "cover");
	
	selected_sites = new Array();
	
	$(".icon-menu").click(toggleMenu);
	$("#open-login-dialog").click(openLoginDialog);
	$("#open-register-tab").click(openRegisterTab);
	$("#view-sites").click(viewSites);
    $("#logout").click(logout);
    $("#open-man").click(openManual);
    $("#open-about").click(openAboutUs);
    $("#desktop-tools").mouseover(showDesktopTools);
    $("#desktop-tools").mouseout(hideDesktopTools);
    $("#add-desktop").click(addDesktop);
    $("#del-desktop").click(delDesktop);
    $("#last-desktop").click(lastDesktop);
    $("#next-desktop").click(nextDesktop);
    $("#add-widget").click(openAddWidgetDialog);
    $("#btn-addwidget").click(addWidget);
    
    //添加鼠标滚动事件
    var dDesktop = document.getElementById("desktop");
    if(document.addEventListener){
		dDesktop.addEventListener("DOMMouseScroll", function(e){
			changeDesktop(e, e.detail * -40);
		}, true);
		dDesktop.addEventListener("mousewheel", function(e){
			changeDesktop(e, e.wheelDelta);
		}, true);
	}else if(document.attachEvent){
		dDesktop.attachEvent("onmousewheel", function(e){
			changeDesktop(e, e.wheelDelta);
		});
	}
});

//打开菜单栏
function toggleMenu(event){
	$(".menu-items").toggle("slow");
    $("#widget-container").toggle("slow");
}

//打开注册标签页
function openRegisterTab(event){
	window.open("index.php?fun=register", "_blank");
}

//浏览网址
function viewSites(event){
	$("#dialog-view-sites").show();
}

//重新加载桌面
function reloadDesktop(desktop){
    $("#desktop").load(DESKTOP_URL, {desktop: desktop}, function(){
        //注册监听器
        $(".icon>img").error(function(){
            $(this).attr("src", ICON_PATH + "/default.png");
        });
        
        //删除图标
        $(".icon").contextMenu("contextmenu-icon", {
            bindings: {
                "del": function(t){
                    confirmDelIcon($(t));
                }
            }
        });
        //打开网址
        $(".icon").click(function(){
            var url = $(this).children("input[name=url]").val();
            window.open(url, "_blank");
        });
    });
}

//确认删除图标
function confirmDelIcon(target){
    var result = confirm("您确定要删除该图标？");
    if(!result)
        return;
    
    var desktop = currentDesktop;
    var index = target.children("input[name=index]").val();
    
    var command = new Command(
        "user_controller",
        "UserController",
        "execDelIcon",
        {desktop: desktop, index: index}
    );
    command.send(handleDelIcon);
}

//处理删除图标的结果
function handleDelIcon(msg){
    if(msg.no == msg.MSG_SUCCESS){
        var desktop = currentDesktop;
        reloadDesktop(desktop);
    }
    
    showTips([msg.content]);
}

//注销
function logout(event){
    var command = new Command(
        "user_controller",
        "UserController",
        "execLogout",
        null
    );
    command.send(function(msg){
		if(msg.no == msg.MSG_SUCCESS){
			delCookie("id");
			delCookie("key");
			window.location.reload();
		}
		
		showTips([msg.content]);
	});
}

//显示桌面工具栏
function showDesktopTools(event){
    $(".desktop-tool").show();
}

//隐藏桌面工具栏
function hideDesktopTools(event){
    $(".desktop-tool").hide();
}

//添加桌面
function addDesktop(event){
	var command = new Command(
		"user_controller",
		"UserController",
		"execAddDesktop",
		null
	);
	command.send(msgHandler);
}

//切换桌面
function changeDesktop(event, delta){
	if(Math.abs(delta) < 120 || scrolling)
		return;
		
	//定位
	scrolling = true;
	var index = 0;
	if(delta > 0){
		lastDesktop(event);
	}else if(delta < 0){
		nextDesktop(event);
	}
	
	setTimeout("scrolling=false;", 500);
	
	return false;
}

//切换到上一个桌面
function lastDesktop(event){
	var index = array_index(desktops, currentDesktop);
	index--;
	index = (index < 0)?desktops.length-1:index;
	
	$("#desktop").hide();
	reloadDesktop(desktops[index]);
	currentDesktop = desktops[index];
	$("#desktop").show();
}

//切换到下一个桌面
function nextDesktop(event){
	var index = array_index(desktops, currentDesktop);
	index++;
	index = (index >= desktops.length)?0:index;
	
	$("#desktop").hide();
	reloadDesktop(desktops[index]);
	currentDesktop = desktops[index];
	$("#desktop").show();
}

//删除桌面
function delDesktop(event){
	var result = confirm("您确定要删除当前桌面吗？");
	if(!result)
		return;
		
	var command = new Command(
		"user_controller",
		"UserController",
		"execDelDesktop",
		{desktop: currentDesktop}
	);
	command.send(msgHandler);
}

//打开添加控件窗口
function openAddWidgetDialog(event){
	$("#dialog-addwidget").show();
	reloadListWidgets();
}

//重新加载控件列表
function reloadListWidgets(){
	$("#list-widgets").load(WIDGETLIST_URL, null, function(){
		$(".line-widget").click(selectWidget);
	});
}

//选择控件
function selectWidget(event){
	var widget = $(this).children("input[name=widget]").val();
	selectedWidget = widget;
	
	$(".line-widget").css("background-color", "");
	$(this).css("background-color", "lightblue");
}

//添加控件
function addWidget(event){
	if(selectedWidget == -1){
		showTips(['请选择您需要添加的控件']);
		return;
	}
	
	var command = new Command(
		"user_controller",
		"UserController",
		"execAddWidget",
		{widget: selectedWidget}
	);
	command.send(handleAddWidget);
}

//处理添加控件
function handleAddWidget(msg){
	if(msg.no == msg.MSG_SUCCESS){
		reloadMyWidgets();
		$("#dialog-addwidget").hide();
	}
	
	showTips([msg.content]);
}

//重新加载用户控件列表
function reloadMyWidgets(){
	$("#widgets").load(MYWIDGETS_URL, null, function(){
		$(".widget-header-close").click(closeWidget);
		$(".widget-header-pop").click(openWidget);
	});
}

//关闭控件
function closeWidget(event){
	var widget = $(this).parents(".widget").children("input[name=widget]").val();
	var command = new Command(
		"user_controller",
		"UserController",
		"execRemoveWidget",
		{widget: widget}
	);
	command.send(handleRemoveWidget);
}

//处理移除控件
function handleRemoveWidget(msg){
	if(msg.no == msg.MSG_SUCCESS){
		reloadMyWidgets();
	}
	
	showTips([msg.content]);
}

//在新窗口打开控件
function openWidget(event){
	var widgetLink = $(this).parents(".widget").children("input[name=widget_link]").val();
	window.open(widgetLink, "_blank");
}

//打开用户手册
function openManual(event){
	window.open("man.html", "_blank");
}

//打开网址信息页面
function openAboutUs(event){
	window.open("about_us.html", "_blank");
}

//自动登录
function autoSigned(){
	var key = getCookie("key");
	var id = getCookie("id");
	var signed = $("input[name=signed]").val();
	if(key == "" || id == "" || signed == "true")
		return;
	
	var command = new Command(
		"user_controller",
		"UserController",
		"execSignIn",
		{key: key, id: id}
	);
	command.send(function(msg){
		if(msg.no == msg.MSG_SUCCESS){
			window.location.reload();
		}
	});
}
