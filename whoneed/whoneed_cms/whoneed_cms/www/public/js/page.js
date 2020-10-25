setTimeout(scrollTo,0,0,0); //隐藏地址栏

//评分星星的颜色改变-开始
$(".starArea li").mouseover(function(){
	var index=$(".starArea li").index(this)+1;
	$("#noteSrc").width(index*40);
});
//评分星星的颜色改变-结束

//提交按钮-开始
function noteWrite(){
	var note=$("#noteSrc").width()/40;
	if(note<=0)
		return false;
	$('#scoreMit').html("提交中...");
	$('#scoreMit').unbind("click");
}
$('#scoreMit').bind('click',noteWrite);
//提交按钮-结束

//精简-开始
$(".js_unfold").each(function(){
	$(this).toggle(function(){
		$(this).prevAll(".js_ufWrap").css("max-height","none");
		$(this).children(".btn_grey").html("精简");
	},function(){
		$(this).prevAll(".js_ufWrap").css("max-height","20px");
		$(this).children(".btn_grey").html("展开");
	});
});
$(".js_ufWrap").each(function(){
	var ufLen = $(this).text().length; 
	if (ufLen > 90 ){
		$(this).next(".js_unfold").show()
	};
});
//精简-结束

// huanghm 04.07 start
//游戏截图的横向滚动-开始
function scr_vsrc(obj){
	var vsrc=obj.attr("data-original");
	obj.attr("src",vsrc); 
}

var oPreviewImg,oLookBigImg,oRecommended;
//自动判定图片宽高,及初始化加载图片-开始
var visibleW = ai.ww(),
	iImgload = 0,
	jietu_ul = ai.i("jietu_ul"),
	oFirstImg = ai.a("#jietu_ul img")[0],
	oNewFirstImg = new Image();
	if (oFirstImg != null) {
            oNewFirstImg.src = oFirstImg.getAttribute("data-original");
	}
if (oFirstImg != null) {
oNewFirstImg.onload = function() {// 第一张图片载入后
	
	if(this.width / this.height > 1){
		$(".js_imgAwh").addClass("ui-page-slideWrap_hor");
		var liWidth=235;
	}
	else{
		$(".js_imgAwh").addClass("ui-page-slideWrap_ver");
		var liWidth=160;
	}
	//延迟加载
	var loadnum=Math.ceil(visibleW/liWidth);	
	var imgall=$(".img");
	for(var i=0;i<loadnum;i++){
		scr_vsrc(imgall.eq(i)); 
	} 
			  
	var spLen = ai.a("#jietu_ul>li").length;
	$("#allPage").html(spLen);
	function onScrollEndEnd(){
		if(iImgload == 0){
			for(var i=loadnum;i<spLen;i++){
				 scr_vsrc(imgall.eq(i)); 
			}
		}
		iImgload = 1;
	}
	//延迟加载
	var spWit = spLen*liWidth;
	ai.i("jietu_ul").style.width = spWit+"px";
	var jietu_ul = ai.i("jietu_ul");
	var scrollBar_jietu = ai.i("scrollBar_jietu");
	
	oPreviewImg = slip('px',jietu_ul,{
		direction: "x",
		bar_no_hide: true,
		touchEndFun: onScrollEndEnd,
		bar_css: "bottom:-1px;border-radius: 10px;",
		perfect: true
	});
	
	
};	
//自动判定图片宽高,及初始化加载图片-结束
//游戏截图的横向滚动-结束



//图片查看器-开始
var iTouchClickNum = 1;
var iShowOrhide = 0;
var oPptImgBg = ai.i("pptImg-bg");
function index_page(obj, current){ // 取得元素在同辈中的索引值
	for (var i = 0, length = current.length; i<length; i++) { 
		if (current[i] == obj ) { 
			return i; 
		} 
	} 
}
ai.touchClick(jietu_ul,function(e){
	if(jietu_ul.webkitMatchesSelector.call( e.target, 'ul img') ) {
		var that =  e.target;
		if(iTouchClickNum ==1 ){
			iTouchClickNum++;
			var visibleH = ai.wh();
			var visibleW = ai.ww();
			var oPptImgUl = ai.i("ui-pptImg-ul");
			var ojietuUlImgLength = ai.a("#jietu_ul img").length;
			
			oPptImgBg.style.display = "block";
			iShowOrhide = 1;
			oPptImgBg.style.marginTop=document.body.scrollTop-1+'px';
			oPptImgBg.style.height=visibleH+3+'px';
			
			for(var i = 0; i < ojietuUlImgLength; i++){
				var oNewLi = document.createElement('li');
				oNewLi.style.width = visibleW+"px";
				oNewLi.innerHTML = "<div class='loading'> <i class='loading-child'></i></div>";
				oPptImgUl.appendChild(oNewLi); 
			}
			var oLilist = ai.a("#ui-pptImg-ul li");
			var ojietuUlImg = ai.a("#jietu_ul img");
			var ojietuUlImgHtml = '';
			var winbili=visibleW/visibleH;
			var nt = document.getElementById("nt").value;
			for(var i = 0; i < ojietuUlImgLength; i++){
				var src = null;;
                                if (nt == "0" || nt == "1") {
                                    src = ojietuUlImg[i].getAttribute("data-original");
                                } else {
                                    src = ojietuUlImg[i].getAttribute("data-original").replace("_","");
                                }
	
				var newPic = new Image();
				newPic.src = src;
				newPic.num = i;
				
				newPic.onload = function(){
					var oliW = this.width;//取得图片实际的宽
					var oliH = this.height;//取得图片实际的
					var imgbili=oliW/oliH; //取得图片的宽和高的比例
					if(oliW > visibleW || oliH > visibleH){
						
						if(imgbili > winbili){//将图片的宽度和屏幕的宽度等宽，再按比例输出图片的高度
							var iImgWidth = visibleW;
							var iImgHeight = visibleW/imgbili;
						}else{//将图片的高度和屏幕的高度等高，再按比例输出图片的宽度
							var iImgWidth = visibleH*imgbili;
							var iImgHeight = visibleH;
						}
					}else{
						var iImgWidth = oliW;
						var iImgHeight = oliH;
					}
					
					oLilist[this.num].innerHTML = '';
					this.width = iImgWidth;
					this.height = iImgHeight;
					this.className = "img";
					oLilist[this.num].appendChild(this);
					
				}
			}
			
			var olastFirstPage = ai.i("lastFirstPage");
			function lastPage(){
				olastFirstPage.innerHTML = "这是最后一张了！";
				olastFirstPage.style['webkitTransitionDuration'] = '300ms';
				olastFirstPage.style['webkitTransform'] = 'translate3d(-70px, -70px, 0px)';
				setTimeout(function(){
					olastFirstPage.style['webkitTransform'] = 'translate3d(-70px, -200px, 0px)';
				},1000);
			}
			function firstPage(){
				olastFirstPage.innerHTML = "这才第一张！";
				olastFirstPage.style['webkitTransitionDuration'] = '300ms';
				olastFirstPage.style['webkitTransform'] = 'translate3d(-70px, -70px, 0px)';
				setTimeout(function(){
					olastFirstPage.style['webkitTransform'] = 'translate3d(-70px, -200px, 0px)';
				},1000);
			}
			function changeEnd(){
				ai.i("numPage").innerHTML = this.page+1;
			}
			oLookBigImg = slip('page',oPptImgUl,{
				num: ojietuUlImgLength,
				no_follow: true,
				lastPageFun: lastPage, 
				firstPageFun: firstPage,
				endFun: changeEnd
			});
			var iIndex = index_page(that.parentNode.parentNode, ai.a("#jietu_ul li"));
			
			ai.i("numPage").innerHTML = iIndex+1;
			oLookBigImg.toPage(iIndex, 0);
			
			ai.touchClick(oPptImgBg,function(e){
				setTimeout(function() {
					oPptImgBg.style.display = "none";
					iShowOrhide = 0;
				},100);
			});
			ai.touchMovePreventDefault(oPptImgBg);
		}else{
			var iIndex = index_page(that.parentNode.parentNode,ai.a("#jietu_ul li"))
			ai.i("numPage").innerHTML = iIndex+1;
			
			oPptImgBg.style.display = "block";
			oPptImgBg.style.marginTop=document.body.scrollTop-1+'px';
			oPptImgBg.style.height=ai.wh()+3+'px';
			oLookBigImg.refresh();
			oLookBigImg.toPage(iIndex, 0);
		}
		
	}
	
});
//图片查看器-结束
}
/*
//近期热门的横向滚动-开始
var loadiconum=Math.ceil(visibleW/90);		  
var loadiconumjishu=0;
var imgicoall=$(".iocimg");
var imgicoalllenght=imgicoall.length;
	
for(var i=0;i<loadiconum;i++){//加载用户可见图标
	scr_vsrc(imgicoall.eq(i)); 
	loadiconumjishu+=1;
} 
function IcoonScrollEndEnd(){
    for(var i=loadiconum;i<imgicoalllenght;i++){
		scr_vsrc(imgicoall.eq(i));
	} 
}
$(".mIco").each(function(){
	var spLen = $(this).find("li").length;
	var spWit = spLen*90;
	$(this).width(spWit);
});
var remen_ul = document.getElementById("remen_ul");

oRecommended = slip('px',remen_ul,{
	direction: "x",
	bar_no_hide: true,
	bar_css: "bottom:-1px;border-radius: 10px;",
	startFun: IcoonScrollEndEnd,
	perfect: true
	
});
//近期热门的横向滚动-结束

ai.resize(function(){
	oPreviewImg.refresh();
	oRecommended.refresh();
	$("#ui-pptImg-ul li").width(ai.ww());
	if(iShowOrhide == 1){
		$("#ui-pptImg-ul img").each(function() {
			var newPic = new Image();
			newPic.src = $(this).attr("src");
			var oliW = newPic.width;//取得图片实际的宽
			var oliH = newPic.height;//取得图片实际的
			var imgbili=oliW/oliH; //取得图片的宽和高的比例]
			var visibleH = ai.wh();
			var visibleW = ai.ww();
			var winbili=visibleW/visibleH;
			if(oliW > visibleW || oliH > visibleH){
				if(imgbili > winbili){//将图片的宽度和屏幕的宽度等宽，再按比例输出图片的高度
					var iImgWidth = visibleW;
					var iImgHeight = visibleW/imgbili;
				}else{//将图片的高度和屏幕的高度等高，再按比例输出图片的宽度
					var iImgWidth = visibleH*imgbili;
					var iImgHeight = visibleH;
				}
			}else{
				var iImgWidth = oliW;
				var iImgHeight = oliH;
			}
			$(this).css("width", iImgWidth + "px");
			$(this).css("height", iImgHeight + "px");
					
        });
		oLookBigImg.refresh();
		oLookBigImg.toPage(ai.i("numPage").innerHTML-1, 0);
		oPptImgBg.style.marginTop = document.body.scrollTop-1+'px';
		oPptImgBg.style.height = ai.wh()+3+'px';
	}
});
*/
// huanghm 04.07 end
//1220隐藏显示
var news_sh_a_h=$(".news_sh_a_h");
var news_sh_b_h=$(".news_sh_b_h");

if(news_sh_a_h&&news_sh_a_h){
	news_sh_a_h.click(function(){
		$(this).parent().find("a").removeClass("on");
		$(this).addClass("on");
		$(this).parent().parent().next().find(".news_sh_a").removeClass("ms-none");
		$(this).parent().parent().next().find(".news_sh_b").addClass("ms-none");
	});
	news_sh_b_h.click(function(){
		$(this).parent().find("a").removeClass("on");
		$(this).addClass("on");
		$(this).parent().parent().next().find(".news_sh_b").removeClass("ms-none");
		$(this).parent().parent().next().find(".news_sh_a").addClass("ms-none");
	});
}


 /*
	内容页标题超出时左右滚动
	@author:lilh3
   */

   function getId(obj)
    {
        return typeof  obj =="string" ? document.getElementById(obj) : obj;
    }
    var RESIZE_EV = 'onorientationchange' in window ? 'orientationchange' : 'resize';
    window.addEventListener(RESIZE_EV, function() {
		autoPlay();
	}, false);
    var t_Time=null;
    var g_title=getId("js_title");
    var g_p=getId("rp");
    var index=1;
    function autoPlay(){
        //alert('有运行');
		var g_body_s=document.documentElement.clientWidth || document.body.offetWidth;
		//alert('g_body_s='+g_body_s);
		var g_width=g_body_s - 80;
        //alert('g_width='+g_width);
		g_title.style.width=g_width + "px";
         //alert('g_p.offsetWidth='+g_p.offsetWidth);
		var gw_width=parseInt(g_p.offsetWidth);
		//alert('gw_width='+gw_width);
		//alert('gw_width >= g_width'+gw_width+'-'+g_width);
		if(gw_width >= g_width)
		{
			clearInterval(t_Time);
			t_Time=setInterval(function(){
				var pos=index;
				if(pos >= gw_width )
				{
					index = -(pos-50);
				}
				else
				{
					g_p.style.cssText="-webkit-transform:translate3d("+ - pos +"px, 0px, 0px);";
					index++;
				}
			},20);
		}
		else
		{
			if(t_Time) clearInterval(t_Time);
			g_p.style.cssText="";
		}
    }
    autoPlay();//lilh3--标题超出滚动
/*标题超出时end*/

/*越狱标签*/
function setCookie(cookieName, cookieValue, days, path, domain, secure) {
    var expires = new Date();
    expires.setDate(expires.getDate() + days);
    document.cookie = escape(cookieName) + '=' + escape(cookieValue)
        + (expires ? '; expires=' + expires.toGMTString() : '')
        + (path ? '; path=' + path : '/')
        + (domain ? '; domain=' + domain : '')
        + (secure ? '; secure' : '');
}
    var cookie_time=365;
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
            window.location=window.location.href+gourl_ary[cidx];
        });
    });
/*越狱标签end*/


//评论---顶
function up(comment_id){
	$.ajax({
		url:"/support/"+comment_id+".html",
		complete:function(data,status){
			$("#up_"+comment_id)[0].innerHTML = parseInt($("#up_"+comment_id)[0].innerHTML) + 1;
		}
	})

   // $("#up_link_"+comment_id).removeAttr('onclick');
}

//评论---踩
function down(comment_id){
	$.ajax({
		url:"/unsupport/" + comment_id+".html",
		complete:function(data,status){
			$("#down_"+comment_id)[0].innerHTML = parseInt($("#down_"+comment_id)[0].innerHTML) + 1;
		}
	});
  //  $("#down_link_"+comment_id).removeAttr('onclick');
}