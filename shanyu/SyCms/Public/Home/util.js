/*
封装JQ函数以Yu开头
*/

/*返回顶部[1]*/
var $backToTopTxt="返回顶部";
var $backToTopEle=$('<a href="javascript:void(0)" class="toTop" title=backToTopTxt alt=backToTopTxt></a>').appendTo($("body")).text($backToTopTxt).attr("title", $backToTopTxt).click(function(){$("html, body").animate({ scrollTop: 0 }, 120);}), $backToTopFun = function() {var st = $(document).scrollTop(), winh = $(window).height();(st > 0)? $backToTopEle.show(): $backToTopEle.hide();/*IE6下的定位*/if(!window.XMLHttpRequest){$backToTopEle.css("top", st + winh - 166);}};
$(window).bind("scroll", $backToTopFun);
$backToTopFun();

/*栏目折叠[2]*/
jQuery.YuFold = function(obj,obj_c,speed,obj_type,Event){
	if(obj_type == 2){
		$(obj+":first").find("b").html("-");
		$(obj_c+":first").show();
	}
	$(obj).bind(Event,function(){
		if($(this).next().is(":visible")){
			if(obj_type == 2){
				return false;
			}
			else{
				$(this).next().slideUp(speed).end().removeClass("selected");
				$(this).find("b").html("+");
			}
		}
		else{
			if(obj_type == 3){
				$(this).next().slideDown(speed).end().addClass("selected");
				$(this).find("b").html("-");
			}else{
				$(obj_c).slideUp(speed);
				$(obj).removeClass("selected");
				$(obj).find("b").html("+");
				$(this).next().slideDown(speed).end().addClass("selected");
				$(this).find("b").html("-");
			}
		}
	});
}
/*5个参数顺序不可打乱，分别是：相应区,隐藏显示的内容,速度,类型,事件*/
//$.YuFold("#Fold .item .tit","#Fold .item .con","fast",2,"click");

/*栏目折叠[3]*/
jQuery.YuTab =function(tabBar,tabCon,class_name,tabEvent,i){
	var $tab_menu=$(tabBar);
	// 初始化操作
	$tab_menu.removeClass(class_name);
	$(tabBar).eq(i).addClass(class_name);
	$(tabCon).hide();
	$(tabCon).eq(i).show();
		
	$tab_menu.bind(tabEvent,function(){
		$tab_menu.removeClass(class_name);
		$(this).addClass(class_name);
		var index=$tab_menu.index(this);
		$(tabCon).hide();
		$(tabCon).eq(index).show();
	});
}
//#tab_demo 父级id,#tab_demo .tabBar span 控制条,#tab_demo .tabCon 内容区,click 事件 点击切换，可以换成mousemove 移动鼠标切换,1	默认第2个tab为当前状态（从0开始）
//$.YuTab("#tab_demo .tabBar span","#tab_demo .tabCon","current","click","1");

/*按钮10秒后可重新点击*/
jQuery.YuTimeBtn = function(name,time) {
	$(name).attr('disabled',true);
	var t   = time;
	var val = $(name).val();
	var timeFun = function(){
		if(t == 0){
			$(name).val(val);
			$(name).removeAttr("disabled");
			clearInterval(s);
			return;
		}else{
			t--;
			$(name).val(t+'秒后可重新点击');
		}
	};
	var s = setInterval(timeFun,1000);
	return;
}
//$.YuTimeBtn('.TimeBtn',10);

/*添加收藏*/
function addFavorite(){
	var sURL=window.location, 
		sTitle=document.title; 
    try{window.external.addFavorite(sURL, sTitle);}
    catch (e)
    {
        try{window.sidebar.addPanel(sTitle, sURL, "");}
        catch (e)
        {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}
/*设为首页*/
function setHome(){
    if(document.all){
    	document.body.style.behavior = 'url(#default#homepage)'; 
    	document.body.setHomePage(document.URL); 
    }else{
    	alert("设置首页失败，请手动设置！");
    } 
}
