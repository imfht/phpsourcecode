//点击游戏列表出现操作按钮
function listinfo_click(that,t_href){
    window.location=t_href;
    return;
    if($(that).next().attr("class").indexOf("drop_down")<0 || $(that).next().find("a").length == 0){
        window.location=t_href;
        return;
    }
    if($(that).next().height()==0){
        $(that).parent().find(".drop_down").height(0);
        $(that).parent().find('.ui-arrow').hide();
        $(that).next().height(45);
        $(that).find('.ui-arrow').show();
    }else{
        $(that).next().height(0);
        $(that).find('.ui-arrow').hide();
    }
}
function setCookie(cookieName, cookieValue, days, path, domain, secure) {
    var expires = new Date();
    expires.setDate(expires.getDate() + days);
    document.cookie = escape(cookieName) + '=' + escape(cookieValue)
        + (expires ? '; expires=' + expires.toGMTString() : '')
        + (path ? '; path=' + path : '/')
        + (domain ? '; domain=' + domain : '')
        + (secure ? '; secure' : '');
}
common.dom.Ready(function(){
//越狱一键安装不跳转
$(".no_jump").click(function(e){
	e.stopPropagation();
});
var cookie_time=365;
//越狱标签
    var gourl_ary=["?wk=yy&text=iph_yyxs_yes","?wk=bb&text=iph_yyxs_no"];
    $(".js_wk").click(function(){
        t_style=$(this).next(".wk_box").attr("style");
        if(typeof(t_style) == "string" && t_style.indexOf("display: none")>=0)
            $(this).next(".wk_box").show();
        else
        $(this).next(".wk_box").hide();
    });
    $(".js_wkIco").each(function(){
        $(this).click(function(){
            $(this).addClass("on").siblings().removeClass("on");
            var cidx=$(".js_wkIco").index(this);
            setCookie("wk",gourl_ary[cidx].substr(gourl_ary[cidx].indexOf("wk=")+3,2), cookie_time,"/",".9game.cn");
            setCookie("i_last_choose",cidx, cookie_time,"/",".9game.cn");
            window.location=gourl_ary[cidx];
        });
    });
//越狱标签
setCookie("is_first_in","yes", cookie_time,"/","");
//帮助
$(".js_fadein").each(function(){
    $(this).click(function(){
        var $fdWrap = $(this).parents(".js_fdiWrap");
        $fdWrap.hide();
        return false;
    });
});
//自动生成标签
$(".js_autoInfo").each(function(){
    $(this).after(function(){
        return '<span class="info">' + $(this).attr('alt') + '</span>';
    });
});
setTimeout(scrollTo,0,0,0);
var rem_items=[];
function loadGameLogo(first_idx,secd_idx){
    var cur_item=first_idx*3+secd_idx-1;
    if(1 == secd_idx || rem_items.indexOf(cur_item) >= 0)
        return;
    rem_items[rem_items.length]=cur_item;
    var src_idx=0;
    $(".game_list_div_scroll").eq(first_idx).find("ul").eq(secd_idx-1).find("img").each(
        function(){
            this.src=imglink_list[cur_item][src_idx];
            src_idx++;
        }
    );
}
//点击游戏列表出现操作按钮
var screenWidth=window.innerWidth;
var game_list_div_scroll=document.getElementsByClassName('game_list_div_scroll');
function echoSpanNav(n){var h='';for(i=1;i<n;i++){h+="<span></span>"}return h;}
function changeEndGameList(){
    var idx=this.curItem,
        x=76*(this.currPageX-1);
    document.getElementById('sub_f_'+idx).style['webkitTransform'] = 'translate3d('+x+'px, 0px, 0px)';;
    document.querySelector('#game_list_'+idx+' > a.on').className = 'item_'+idx+' item';
    document.querySelector('#game_list_'+idx+' > a:nth-child(' + (this.currPageX) + ')').className = 'item_'+idx+' item on';
    document.querySelector('#dot-group-'+idx+' > span.on').className = '';
    document.querySelector('#dot-group-'+idx+' > span:nth-child(' + (this.currPageX) + ')').className = 'on';
    setTimeout(loadGameLogo,300,this.curItem,this.currPageX);
}
function windowResize(){
    var initialX=-(this.currPageX-1)*window.innerWidth;
    game_list_div_scroll[this.curItem].style['webkitTransform'] = 'translate3d('+initialX+'px, 0px, 0px)';
    this.screenWidth=window.innerWidth;
};
var scrollAry=[];
for(var idx=0;idx<game_list_div_scroll.length;idx++){
    var screenNum=document.getElementsByClassName('item_'+idx).length;
    if( idx == 4)
        $(".game_list_div_scroll").eq(idx).width(''+screenNum*100+'%').find('.information_ul').width(''+100/screenNum+'%');//改变对应的宽度
    else
        $(".game_list_div_scroll").eq(idx).width(''+screenNum*100+'%').find('ul').width(''+100/screenNum+'%');//改变对应的宽度
    var dot_group=echoSpanNav(screenNum);
    document.getElementById('dot-group-'+idx).innerHTML='<span class="on"></span>'+dot_group+'';
    $('.item_'+idx).each(function(){
        var PPTindex=$(".item_"+idx).index(this);
        $(this).click(function(){
            var idx=this.className.substr(this.className.indexOf("_")+1,1);
            scrollAry[idx].goNum(PPTindex);
        });
    });
    scrollAry[scrollAry.length]=new tScroll({
        obj:game_list_div_scroll[idx],//要移动的对象
        curItem:idx,//第几个对象，用于回调函数传值
        limitX:true,//只x轴滑动
        change:true,//状态为换屏
        screenWidth:screenWidth,//一屏的大小
        screenNum:screenNum,//屏数
        onScrollEnd:changeEndGameList,//轮换结束时执行函数
        windowResize:windowResize,//窗口尺寸改变时执行的函数（手机横向和纵向摆放）
        pullLock:true// 不阻碍页面上下滑动
    });
}
//焦点图轮换
var changImg = document.getElementById("change_img");
var change_img_nav_ul = document.getElementById("change_img_nav_ul");
var change_img_nav = document.getElementById("change_img_nav");
var changImgLength=changImg.getElementsByTagName('li').length;
changImg.style.cssText='width:'+changImgLength*screenWidth+'px;';
function echoLi(n){var html='';for(i=1;i<n;i++){html+="<li></li>"}return html;}
var html=echoLi(changImgLength);
change_img_nav_ul.innerHTML='<li class="active"></li>'+html+'';
//图片延迟加载-开始
function scr_vsrc(obj){
	var vsrc=obj.attr("data-original");
	obj.attr("src",vsrc); 
}
var imgall=$(".focus_img");
var imgalllength=imgall.size();
var loadnumjishu=0;
scr_vsrc(imgall.eq(0)); 
setTimeout(function(){//页面载入n秒后用户不可见图片加载
  if(loadnumjishu<imgalllength-1){
	for(var i=loadnumjishu;i<imgalllength;i++){
		 scr_vsrc(imgall.eq(loadnumjishu)); 
		 loadnumjishu+=1;
	}
  }
},5000);
function changeEndChangImg(){//图片轮换时按钮变化
	if(loadnumjishu<(this.currPageX-1)){
		loadnumjishu=this.currPageX-1;
		scr_vsrc(imgall.eq(this.currPageX-1));
	}
	document.querySelector('#change_img_nav_ul > li.active').className = '';
	document.querySelector('#change_img_nav_ul > li:nth-child(' + (this.currPageX) + ')').className = 'active';
}
//图片延迟加载-结束
new tScroll({
	obj:changImg,//要移动的对象
	limitX:true,//只x轴滑动
	change:true,//状态为换屏
	screenWidth:320,//一屏的大小
	screenNum:changImgLength,//屏数
	ingTime:6000,//自动轮换时间
	onScrollEnd:changeEndChangImg,//轮换结束时执行函数
	pullLock:true// 不阻碍页面上下滑动
});
//焦点图轮换

});