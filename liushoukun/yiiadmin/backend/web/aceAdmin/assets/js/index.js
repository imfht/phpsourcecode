/* ------------------------------------------------------------------
 * 
 * 	全局变量
 * 
 * ------------------------------------------------------------------*/
$task_content_inner = null;
$mainiframe=null;
var tabwidth=118;
$loading=null;
// sidebar左边bar栏
//$nav_wraper=$("#nav_wraper");

/* ------------------------------------------------------------------
 * 
 * 	执行
 * 
 * ------------------------------------------------------------------*/
$(function () {
	//
	$mainiframe=$("#mainiframe");
	// 选项卡内容区
	$content=$("#content");
	// 加载时提示
	$loading=$("#loading");
	
	// 头部高度
	var headerheight=86;
	// 选项卡内容区高度
	$content.height($(window).height()-headerheight);
	
	// sidebar左边bar栏高度
//	$nav_wraper.height($(window).height()-45);
//	$nav_wraper.css("overflow","auto");
	
	//$nav_wraper.niceScroll();
	// 调整浏览器窗口的大小触发resize事件
	$(window).resize(function(){
//		$nav_wraper.height($(window).height()-45);
		$content.height($(window).height()-headerheight);
		 calcTaskitemsWidth();
	});
	
	// #content iframe加载完成时执行：加载提示隐藏
	$("#content iframe").load(function(){
    	$loading.hide();
    });
	
	// 选项卡：任务栏ul容器ID
    $task_content_inner = $("#task-content-inner");
   
	// 当按钮被松开时，发生 keyup事件
    $("#searchMenuKeyWord").keyup(function () {
        var wd = $(this).val();
        //searchedmenus
        var $tmp = $("<div></div>");
        if (wd != "") {
            $("#allmenus a:contains('" + wd + "')").each(
        function () {
            $clone = $(this).clone().prepend('<img src="/images/left/01/note.png">');

            $clone.wrapAll('<div class="menuitemsbig"></div>').parent().attr("onclick", $clone.attr("onclick")).appendTo($tmp);

        }
        );
            $("#searchedmenus").html($tmp.html());
            $("#searchedmenus").show();
            $("#allmenus").hide();
            $("#defaultstartmenu").hide();
            $("#allmenuslink .menu_item_linkbutton").html("返回");
            isAllDefault = false;
            // $("#searchedmenus").html($tmp).show();

        }

    });

    

    $("#appbox  li .delete").click(function (e) {
        $(this).parent().remove();
        return false;
    });

   

    ///

//  $(".apps_container li").live("click", function () {
    $("body").on("click",".apps_container li", function () {
        var app = '<li><span class="delete" style="display:inline">×</span><img src="" class="icon"><a href="#" class="title"></a></li>';
        var $app = $(app);
        $app.attr("data-appname", $(this).attr("data-appname"));
        $app.attr("data-appid", $(this).attr("data-appid"));
        $app.attr("data-appurl", $(this).attr("data-appurl"));
        $app.find(".icon").attr("src", $(this).attr("data-icon"));
        $app.find(".title").html($(this).attr("data-appname"));
        $app.appendTo("#appbox");
        $("#appbox  li .delete").off("click");
        $("#appbox  li .delete").click(function () {
            $(this).parent().remove();
            return false;
        });
    });

    ///
    $("#tdshortcutsmor1").click(function () {
        $(".window").hide();
    });

	$("body").on("click",".task-item", function () {
//  $(".task-item").live("click", function () {
        var appid = $(this).attr("app-id");
        var $app = $('#' + appid);
        showTopWindow($app);
    });

	$("body").on("click","#task-content-inner li", function () {
//  $("#task-content-inner li").live("click", function () {
    	openapp($(this).attr("app-url"), $(this).attr("app-id"), $(this).attr("app-name"));
    	return false;
    });
    
    $("body").on("dblclick","#task-content-inner li", function () {
//  $("#task-content-inner li").live("dblclick", function () {
    	closeapp($(this));
    	return false;
    	
    });
    
    $("body").on("click","#task-content-inner a.macro-component-tabclose", function () {
//  $("#task-content-inner a.macro-component-tabclose").live("click", function () {
    	closeapp($(this).parent());
        return false;
    });
    
    $("#task-next").click(function () {
        var marginleft = $task_content_inner.css("margin-left");
        marginleft = marginleft.replace("px", "");
        var width = $("#task-content-inner li").length * tabwidth;
        var content_width = $("#task-content").width();
        var lesswidth = content_width - width;
        marginleft = marginleft - tabwidth <= lesswidth ? lesswidth : marginleft - tabwidth;

        $task_content_inner.stop();
        $task_content_inner.animate({ "margin-left": marginleft + "px" }, 300, 'swing');
    });
    $("#task-pre").click(function () {
        var marginleft = $task_content_inner.css("margin-left");
        marginleft = parseInt(marginleft.replace("px", ""));
        marginleft = marginleft + tabwidth > 0 ? 0 : marginleft + tabwidth;
        // $task_content_inner.css("margin-left", marginleft + "px");
        $task_content_inner.stop();
        $task_content_inner.animate({ "margin-left": marginleft + "px" }, 300, 'swing');
    });
    
    $("#refresh_wrapper").click(function(){
    	var $current_iframe=$("#content iframe:visible");
    	$loading.show();
    	//$current_iframe.attr("src",$current_iframe.attr("src"));
    	$current_iframe[0].contentWindow.location.reload();
    	return false;
    });
	
	// 调整选项卡-任务栏宽度，若长度超出刚显示左右箭头按钮可显示更多
    calcTaskitemsWidth();
});

// 调整选项卡-任务栏宽度，若长度超出刚显示左右箭头按钮可显示更多
function calcTaskitemsWidth() {
    var width = $("#task-content-inner li").length * tabwidth;
    $("#task-content-inner").width(width);
    if (($(document).width()-268-tabwidth- 30 * 2) < width) {
        $("#task-content").width($(document).width() -268-tabwidth- 30 * 2);
        $("#task-next,#task-pre").show();
    } else {
        $("#task-next,#task-pre").hide();
        $("#task-content").width(width);
    }
}

// 关闭当前任务栏标签
function close_current_app(){
	closeapp($("#task-content-inner .current"));
}

// 关闭任务栏标签
function closeapp($this){
	if(!$this.is(".noclose")){
		$this.prev().click();
    	$this.remove();
    	$("#appiframe-"+$this.attr("app-id")).remove();
    	calcTaskitemsWidth();
    	$("#task-next").click();
	}
	 
}




// 选项卡：任务栏标签
var task_item_tpl ='<li class="macro-component-tabitem">'+
					'<span class="macro-tabs-item-text btn btn-info"></span>'+
					'<a class="macro-component-tabclose" href="javascript:void(0)" title="点击关闭标签"><span></span><b class="macro-component-tabclose-icon">×</b></a>'+
					'</li>';

// 选项卡内容区：任务栏标签对应内容iframe
var appiframe_tpl='<iframe style="width:100%;height: 100%;" frameborder="0" class="appiframe"></iframe>';


function openapp(url, appid, appname, refresh) {
    var $app = $("#task-content-inner li[app-id='"+appid+"']");
    $("#task-content-inner .current").removeClass("current");
    if ($app.length == 0) {
        var task = $(task_item_tpl).attr("app-id", appid).attr("app-url",url).attr("app-name",appname).addClass("current");
        task.find(".macro-tabs-item-text").html(appname).attr("title",appname);
        $task_content_inner.append(task);
        $(".appiframe").hide();
        $loading.show();
        $appiframe=$(appiframe_tpl).attr("src",url).attr("id","appiframe-"+appid);
        $appiframe.appendTo("#content");
        $appiframe.load(function(){
        	$loading.hide();
        });
        calcTaskitemsWidth();
    } else {
    	$app.addClass("current");
    	$(".appiframe").hide();
    	var $iframe=$("#appiframe-"+appid);
    	var src=$iframe.get(0).contentWindow.location.href;
    	src=src.substr(src.indexOf("://")+3);
    	/*if(src!=GV.HOST+url){
    		$loading.show();
    		$iframe.attr("src",url);
    		$appiframe.load(function(){
            	$loading.hide();
            });
    	}*/
    	if(refresh===true){//刷新
    		$loading.show();
    		$iframe.attr("src",url);
    		$iframe.load(function(){
            	$loading.hide();
            });
    	}
    	$iframe.show();
    	//$mainiframe.attr("src",url);
    }
    
    // 
    var itemoffset= $("#task-content-inner li[app-id='"+appid+"']").index()* tabwidth;
    var width = $("#task-content-inner li").length * tabwidth;
   
    var content_width = $("#task-content").width();
    var offset=itemoffset+tabwidth-content_width;
    
    var lesswidth = content_width - width;
    
    var marginleft = $task_content_inner.css("margin-left");
   
    marginleft =parseInt( marginleft.replace("px", "") );
    var copymarginleft=marginleft;
    if(offset>0){
    	marginleft=marginleft>-offset?-offset:marginleft;
    }else{
    	marginleft=itemoffset+marginleft>=0?marginleft:-itemoffset;
    }
    
    if(-itemoffset==marginleft){
    	marginleft = marginleft + tabwidth > 0 ? 0 : marginleft + tabwidth;
    }
    
    //alert("cddd:"+(content_width-copymarginleft)+" dddd:"+(-itemoffset));
    if(content_width-copymarginleft-tabwidth==itemoffset){
    	marginleft = marginleft - tabwidth <= lesswidth ? lesswidth : marginleft - tabwidth;
    }
    
	$task_content_inner.animate({ "margin-left": marginleft + "px" }, 300, 'swing');
    
    
    
  
}

