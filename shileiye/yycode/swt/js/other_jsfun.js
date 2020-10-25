/*****************************/
/*         扩展功能函数       */
/*****************************/
//通用关闭窗口函数(清除当前元素的父元素)
function closeWin(o){
	var s=o.parentNode;
	//s.style.display="none";
	document.body.removeChild(s);
}

//监听离线宝回车键提交(给<input>加上onkeydown="lxbtelkeyUp(event,this)"属性可实现回车键提交)
function lxbtelkeyUp(e,o) {
	var e=(e)?e:((window.event)?window.event:"");
	var currKey=e.keyCode?e.keyCode:e.which;
	if(currKey==13){lxb.call(o);o.focus();};
}

//离线宝点击提交(给<a>标签加上onclick="lxbtelcall('lefttel');"可将id="lefttel"的input提交到离线宝)
function lxbtelcall(o) {
	var o=document.getElementById(o);
	lxb.call(o);
	o.focus();
}

//onload执行多函数(同window.onload=function(){...}方法，但可重用无限次)
function $$$(func){
	if (document.addEventListener) {
		window.addEventListener("load", func, false);
	}else if (document.attachEvent) {
		window.attachEvent("onload", func);
	}
}

//移动端访问跳转，调用方法：uaredirect("http://m.xxx.com")
function uaredirect(f){
	if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i))) {
		location.replace(f);
		return true;
	}
}
//检测是否手机访问
function is_mobile_request(){
	if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i))) {
		return true;
	}else{
		return false;
	}
}

//给商务通传参数（调用方法：onclick="gotoswt(event,this,"test")"，test为当前元素说明，可忽略）
function gotoswt(e,o){
	var s=arguments[2]?"zxclick-"+arguments[2]:"zxclick-other";
	//获取上一页网址
	//var referrer = document.referrer;
	var referrer = window.location.href
	if (!referrer) {
		try {
			if (window.opener) {
				referrer = window.opener.location.href;
			} else {
				referrer = window.location.href;
			}
		}
		catch (e) {}
	}
	//获取鼠标相对于文档的坐标
	var e=(e)?e:((window.event)?window.event:"");
	var scrollX=document.documentElement.scrollLeft || document.body.scrollLeft;
	var scrollY=document.documentElement.scrollTop || document.body.scrollTop;
	var xy=Math.round(e.clientX+scrollX)+","+Math.round(e.clientY+scrollY);  
	//获取元素ID、CSS等名称
	var uid="",ucss="";
	if(o.id)uid=o.id;
	if(o.className)ucss=o.className;
	var wh=document.body.scrollWidth+","+document.body.scrollHeight;
	//传入参数
	o.href="{swtdir}/"+"?"+s+"|||"+uid+"|||"+ucss+"|||"+xy+"|||"+wh+"|||"+encodeURIComponent(referrer);
	//清空事件
	//o.setAttribute("onclick",'');
	//o.click("return false");
	cnzzsj(o);
 }
/*
1、增加CNZZ事件统计处理，CNZZ统计ID不为空的情况下，在任意标签加上cnzzsj属性可传入点击事件。
2、例：<a href="tel:010111111" cnzzsj="首页,导航栏,在线咨询">在线咨询</a>
3、注意，属性值三个参数为必填，用半角逗号隔开。gotoswt(w,o)函数已集成本功能，无须多次添加。
*/
function cnzzsj(o){
	if(o.getAttribute("cnzzsj") && info["cnzzid"]!=""){
		var htitle=o.attributes['cnzzsj'].nodeValue.split(',');
		_czc.push(["_trackEvent",htitle[0],htitle[1],htitle[2],"5"]);
	}
}
 //隐藏商务通侧边栏
 function del_swt_left(){
	if (document.getElementById("LRfloater0")) {
		onlinerIcon0.hidden();
	}else{
		setTimeout("del_swt_left()",0);
	}
}
 //强制替换商务通邀请框函数
 function reLR_showInviteDiv(){
	if (isExitsFunction("LR_showInviteDiv")) {
		NewshowInviteDiv();
	}else{
		setTimeout("reLR_showInviteDiv()",0);
	}
}
//是否存在指定函数
function isExitsFunction(funcName) {
	try {
		if (typeof(eval(funcName)) == "function") {
			return true;
		}
	} catch(e) {}
	return false;
}
//是否存在指定变量
function isExitsVariable(variableName) {
	try {
		if (typeof(variableName) == "undefined") {
			return false;
		} else {
			return true;
		}
	} catch(e) {}
	return false;
}
//设置cookies，过期时间为零则关闭浏览器即过期
function setCookie(name, value, expiresHours) {
	var cookieString = name + "=" + escape(value);
	//判断是否设置过期时间 
	if (expiresHours > 0) {
		var date = new Date();
		date.setTime(date.getTime + expiresHours * 3600 * 1000);
		cookieString = cookieString + ";expires=" + date.toGMTString();
	}
	document.cookie = cookieString+";path=/";
}
//读取cookies
function getCookie(name) {
	var strCookie = document.cookie;
	var arrCookie = strCookie.split("; ");
	for (var i = 0; i < arrCookie.length; i++) {
		var arr = arrCookie[i].split("=");
		if (arr[0] == name) return unescape(arr[1]);
	}
	return "";
}
//删除cookies
function delCookie(name) {
	var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cval = getCookie(name);
	if (cval != null) document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}
//重定义商务通弹出框函数
function NewshowInviteDiv(){
	window.LR_showInviteDiv = function(h1, h2){
		if (!LR_showinvite) return;
		if (h1 == null && h2 == null) return;
		if (h1 == '1' && h2 == '1' && LR_chated_no_invite && LR_getCookie('LR_lastchat') == '1') {
			return;
		}
		var LR_ikind1 = (!LR_invite_display_kind || h2 == '1');
		if (typeof(LiveAutoInvite0) != 'undefined' && h1 == '1') h1 = LiveAutoInvite0;
		if (h2 == '1') h2 = LR_GetAutoInvite2();
		if (h1.indexOf('%IP%') != -1) {
			var ipfrom = unescape(LR_ip1);
			if (ipfrom.length < 3 || (LR_ip1 == null && LR_ip2 == null)) {
				h1 = '';
			} else {
				h1 = h1.replace('%IP%', ipfrom);
			}
		}
		LR_cur_invite = h2;
		//LR_m_f(LR_m_d);
		if ((typeof(LR_invite_m) != 'undefined') && LR_invite_m) LR_m_d = LR_m_e();
		if (LR_UserInviteDiv != null && LR_ikind1) {
			LR_Floaters[1].pms['html'] = LR_UserInviteDiv.replace('{c0}', LR_invite_color0).replace('{c1}', LR_invite_color1).replace('{c2}', LR_invite_color2).replace('{c3}', LR_invite_color3).replace('{aimg}', LR_CheckUserUrl(LR_accept_img)).replace('{fimg}', LR_CheckUserUrl(LR_refuse_img)).replace('{pimg}', LR_CheckUserUrl(LR_ivite_img)).replace('{h1}', h1).replace('{h2}', h2).replace(/\{0\}/g, 'openZoosUrl();LR_HideInvite();').replace(/\{1\}/g, 'LR_RefuseChat();LR_HideInvite();');
		} else {
			onlinerIcon1.pms['closer_show'] = 0;
			if (LR_isMobile && (LR_inviteim.readyState == 'complete' || (LR_inviteim.readyState != 'undefined' && LR_inviteim.complete))) {
				LR_invitew = LR_inviteim.width / 2;
				LR_inviteh = LR_inviteim.height / 2;
			}
			LR_Floaters[1].pms['html'] = newswthtml;
		}
		LR_Floaters[1].showdiv(0);
		LR_Floaters[1].imageTimer(true);
		if (LR_fade_invite) LR_fadeIn('LRfloater1');
		if (document.body) {
			document.body.appendChild(LR_GetObj('LRdiv0'));
			document.body.appendChild(LR_GetObj('LRdiv1'));
		}
		if (LR_invite_hide_float && LR_showfloat) LR_Floaters[0].hidden();
		window.focus();
		LR_SetCookie('lastshowinvite', new Date().getTime(), 720);
	}
}