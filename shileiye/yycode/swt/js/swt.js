/*
通用医院网站JSv150324	By:shileiye
调用方法：<script src="/swt/js/swt.php?rblq"></script>
说明：?后面的参数可选
*/
/*****************************/
/*          商务通配置   	     */
/*****************************/
var swtid="DBT78232305";	//商务通ID
var swtidurl=swtid.substr(0,3)+".zoosnet.net";	//商务通网址（一般情况不用设置）
//alert(swtidurl);
var newswt_pc_img="/swt/img/swt.gif";	//PC邀请框图片地址
var newswt_pc_w=446;	//PC图片宽度
var newswt_pc_h=327;	//PC图片高度
var lr_xCenter=newswt_pc_w/2;	//邀请框水平位置
var lr_yCenter=newswt_pc_h/2;	//邀请框垂直位置

var newswtclose_pc_img="/swt/img/close.gif";	//PC邀请框关闭按钮图片地址
var newswtclose_pc_right=2;	//PC邀请框关闭按钮距离边框右边位置
var newswtclose_pc_top=2;	//PC邀请框关闭按钮距离边框上边位置
var LiveAutoInvite0='您好，来自%IP%的朋友';
var LiveAutoInvite1=window.location.href;
var LiveAutoInvite2='如果您有什么问题,请点击此处与专家进行即时沟通！';
var LrinviteTimeout=5;	//首次弹出邀请框秒数
var LR_next_invite_seconds =10;	//再次弹出间隔秒数

/*//说明：浮动图标的水平方向对齐方式，1为右对齐，0为左对齐；
var Invite_ToRight=0;
//说明：浮动图标的水平方向的边距；
var Invite_left=400;
//说明：浮动图标的垂直方向对齐方式，1为底部对齐，0为顶部对齐；
var Invite_ToBottom=0;
//说明：浮动图标的垂直方向的边距；
var Invite_top=100;*/

//商务通PC邀请界面代码
var newswt_pc_html='<div id="new_swt_wee" style="width:'+newswt_pc_w+'px; height:'+newswt_pc_h+'px; overflow:hidden; position:relative; z-index:900;"><img href="javascript:void(0)" onclick="LR_HideInvite();LR_RefuseChat();return false;" style="position: absolute; cursor: pointer; right: '+newswtclose_pc_right+'px; top: '+newswtclose_pc_top+'px; display: block; " src="'+newswtclose_pc_img+'"><img title="'+LiveAutoInvite2+'"  alt="'+LiveAutoInvite2+'" src="'+newswt_pc_img+'" style="cursor:pointer" onclick="openZoosUrl();"></div>';

//商务通手机邀请界面代码
//var newswt_sj_html='<div id="new_swt_wee" style="width:'+newswt_sj_w+'px; height:'+newswt_sj_h+'px; overflow:hidden; position:relative; z-index:900;"><img href="javascript:void(0)" onclick="LR_HideInvite();LR_RefuseChat();return false;" style="position: absolute; cursor: pointer; right: '+newswtclose_sj_right+'px; top: '+newswtclose_sj_top+'px; display: block; " src="'+newswtclose_sj_img+'"><img title="'+LiveAutoInvite2+'"  alt="'+LiveAutoInvite2+'" src="'+newswt_sj_img+'" style="cursor:pointer" onclick="openZoosUrl();"></div>';

//隐藏商务通侧边栏
function del_swt(){
	if (document.getElementById("LRfloater0")) {
		onlinerIcon0.hidden();
	}else{
		setTimeout("del_swt()",0);
	}
}
del_swt();

//调用商务通JS及重构函数
document.writeln('<script language="javascript" src="http://'+swtidurl+'/JS/LsJS.aspx?siteid='+swtid+'&float=1"></script>');
document.writeln('<script language="javascript" src="/swt/js/LR_showInviteDiv.js"></script>');
//调用浮动窗口CSS样式
document.writeln("<link rel='stylesheet' href='/swt/css/style.css'>");
/*****************************/
/*        页头菜单栏配置      */
/*****************************/
var swttopBanner_top=158;	//滚动到多少像素显示页头菜单

//页头菜单栏HTML
document.writeln("<div id=\"swttopBanner\">");
document.writeln("     <a href=\"/\" target=\"_blank\">网站首页</a>");
document.writeln("     <a href=\"/yyjj/\" target=\"_blank\">医院概况</a>");
document.writeln("     <a href=\"/qwjs/\" target=\"_blank\">诊疗技术</a>");
document.writeln("     <a href=\"/dxal/\" target=\"_blank\">康复案例</a>");
document.writeln("     <a href=\"/zjtd/\" target=\"_blank\">专家团队</a>");
document.writeln("     <a href=\"/dzjs/\" target=\"_blank\">党政建设</a>");
document.writeln("     <a href=\"/ask/\" target=\"_blank\">遗患问答</a>");
document.writeln("     <a href=\"jiavascript:void(0)\" onclick=\"openZoosUrl();return false;\" target=\"_blank\">预约挂号</a>");
document.writeln("     <a href=\"/lylx/\" target=\"_blank\">来院路线</a>");
document.writeln("</div>");

//滚动条超过设置位置，刷新显示页头菜单
function is_swttopBanner(){
	var swttopBanner_scroll = document.documentElement.scrollTop || document.body.scrollTop;
	if (document.getElementById("swttopBanner")) {
		if(swttopBanner_scroll>=swttopBanner_top) { 
			swttopBanner.style.display="inline"; 
		}else{
			swttopBanner.style.display="none"; 
		}
	}else{
		setTimeout("is_swttopBanner()",0);
	}
}
is_swttopBanner();

//监听页面滚动事件处理菜单显示或隐藏
window.onscroll = function(){ 
	var t=document.documentElement.scrollTop || document.body.scrollTop;  
	var swttopBanner=document.getElementById("swttopBanner"); 
	if(swttopBanner){
		if(t>=swttopBanner_top) { 
			swttopBanner.style.display="inline"; 
		}else{ 
			swttopBanner.style.display="none"; 
		}
	}
}

/*****************************/
/*         左侧边栏配置       */
/*****************************/
document.writeln('<div id="swtleftBanner">');
document.writeln('<img href="javascript:void(0)" onclick="closeWin(this);return false;" style="position: absolute; cursor: pointer; right: '+newswtclose_pc_right+'px; top: '+newswtclose_pc_top+'px; display: block; " src="'+newswtclose_pc_img+'">');
document.writeln('	<a target="_blank" href="/swt/"></a>');
document.writeln('</div>');

/*****************************/
/*         右侧边栏配置       */
/*****************************/
document.writeln('<div id="swtrightBanner">');
document.writeln('<img href="javascript:void(0)" onclick="closeWin(this);return false;" style="position: absolute; cursor: pointer; right: '+newswtclose_pc_right+'px; top: '+newswtclose_pc_top+'px; display: block; " src="'+newswtclose_pc_img+'">');
document.writeln('	<a target="_blank" href="/swt/"></a>');
document.writeln('</div>');

/*****************************/
/*         底部横栏配置       */
/*****************************/
document.writeln('<div id="swtbottomBanner">');
document.writeln('	<a target="_blank" href="/swt/"></a>');
document.writeln('</div>');


/*****************************/
/*        QQ抖动窗口配置      */
/*****************************/
document.writeln('<div id="swtQQBanner">');
document.writeln('<img href="javascript:void(0)" onclick="closeWin(this);return false;" style="position: absolute; cursor: pointer; right: '+newswtclose_pc_right+'px; top: '+newswtclose_pc_top+'px; display: block; " src="'+newswtclose_pc_img+'">');
document.writeln('	<a target="_blank" href="/swt/?qq"></a>');
document.writeln('</div>');

/*****************************/
/*         扩展功能函数       */
/*****************************/
//通用关闭窗口函数
function closeWin(o){
	//o.parentNode.style.display="none";
	var s=o.parentNode;
	document.body.removeChild(s);
}

//QQ抖动函数
function qqdoudong(){
	var obj=document.getElementById('swtQQBanner');
	if(obj){
		var posData=[obj.offsetLeft,obj.offsetTop];
		setInterval(function(){
			var i=0;
			clearInterval(timer);
			var timer=setInterval(function(){
				i++;
				obj.style.right=((i%2)>0?-2:2)+'px';
				obj.style.bottom=((i%2)>0?-2:2)+'px';
				if(i>=25){
					clearInterval(timer);
					obj.style.right='0px';
					obj.style.bottom='0px';
				}
			}, 35);	//抖动速度
		},5000);	//抖动间隔
	}
}
window.onload=function(){qqdoudong();}

//移动端访问处理函数，调用方法：uaredirect("http://m.xxx.com")
function uaredirect(murl){
	try {
		if(document.getElementById("bdmark") != null){
		return;
	}
		var urlhash = window.location.hash;
		if (!urlhash.match("fromapp")){
			if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i))){
				location.replace(murl);
			}
		}
	}
	catch(err){}
}
/*****************************/
/*         统计代码配置       */
/*****************************/
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://"); document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F84b0d83517477a8f55917f4ac48372be' type='text/javascript'%3E%3C/script%3E"));
