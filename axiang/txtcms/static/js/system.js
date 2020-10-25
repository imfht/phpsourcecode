$(function(){
	$("a").bind("focus",function() {
		if(this.blur) {this.blur()};
	});
});
var showDialogST = null;
function showDialog(msg, mode, title, okfunc, lock, cancelfunc, leftmsg, oktxt, canceltxt, closetime, locationtime){
	clearTimeout(showDialogST);//清除定时
	lock = isUndefined(lock) ? (mode == 'info' ? false : true) : lock;
	leftmsg = isUndefined(leftmsg) ? '' : leftmsg;
	mode = in_array(mode, ['confirm', 'notice', 'info', 'success']) ? mode : 'error';
	var menuid = 'art_dialog';
	var menuObj = $('.'+menuid);
	var showconfirm = 1;
	if(isUndefined(msg)) return art.dialog({id:menuid});//弹出一个载入动画
	oktxtdefault = '确定';//默认确定按钮的文字 
	closetime = isUndefined(closetime) ? null : closetime;
	closefunc = function () {
		if(typeof okfunc == 'function') okfunc();
		else eval(okfunc);
	};
	if(closetime) {
		leftmsg = '<span id="closetime">' + closetime + '</span> 秒后窗口关闭<script>lastNum("closetime",' + closetime + ')</script>';
		showDialogST = setTimeout(closefunc, closetime*1000);
		showconfirm = 0;
	}
	locationtime = isUndefined(locationtime) ? '' : locationtime;
	if(locationtime) {
		leftmsg = '<span id="locationtime">' + locationtime + '</span> 秒后页面跳转<script>lastNum("locationtime",' + locationtime + ')</script>';
		showDialogST = setTimeout(closefunc, locationtime*1000);
		showconfirm = 0;
	}
	oktxt = oktxt ? oktxt : oktxtdefault;//确定按钮的文字
	canceltxt = canceltxt ? canceltxt : '取消';//取消按钮的文字
	if(menuObj) art.dialog({id:menuid}).close();
	leftfunc=function () {
		if(leftmsg){
			$('<span style="color: #999999;float: left;line-height: 25px;">'+leftmsg+'</span>').prependTo(".aui_dialog .aui_footer .aui_buttons");
		}
	};
	art.dialog({
		id:menuid,
		title:title ? title : '提示信息',
		time:closetime,
		opacity:0.3,
		lock:lock,
		content: msg,
		icon:(mode=='info' ? '' : mode),
		init:leftfunc,
		okVal:oktxt,
		ok:(mode=='info' ? false : function() {
			if(typeof okfunc == 'function') okfunc();
			else eval(okfunc);
		}),
		cancelVal:canceltxt,
		cancel:(mode!='confirm' ? false : function() {
			if(typeof cancelfunc == 'function') cancelfunc();
			else eval(cancelfunc);
		})
	});
}
function lastNum(id,ii){
	document.getElementById(id).innerHTML=ii;
	if( ii==1 ){
		return false;
	}
	ii--;
	setTimeout("lastNum('"+id+"',"+ii+")",1000);
}
function showAlert(icon,msg,url) {
	var p = /<script[^\>]*?>([^\x00]*?)<\/script>/ig;
	msg = msg.replace(p, '');
	if(msg !== '') {
		if(url){
			showDialog(msg, icon, null,'location.href="'+url+'";',1,null,null,null,null,2,2);
		}else{
			showDialog(msg, icon, null, true, true, null, null, null, null, 2);
		}
	}
}
function showWindow(k, url, mode, cache, v){

}
var lastCtrl = new Object();
function selemenu(ctrl){
	if(ctrl!=lastCtrl){
		lastCtrl.className="left_link";
	}
	ctrl.className="left_link_over";
	lastCtrl = ctrl;
}
function selectTab(showContent,selfObj){
	// 操作标签
	var tag = $("#admin_sub_title")[0].getElementsByTagName("li");
	var taglength = tag.length;
	for(i=0; i<taglength; i++){
		tag[i].className = "unsub";
	}
	selfObj.parentNode.className = "sub";
	// 操作内容
	for(i=0; j=$("#config"+i)[0]; i++){
		j.style.display = "none";
	}
	$('#'+showContent)[0].style.display = "";
}

function checkselect(obj,form){ 
	var bool=(obj.checked)?true:false;
	for(var i=0;i<form.length;i++)
	{ 
		form.all[i].selected=bool;
	} 
}

function isUndefined(variable) {
	return typeof variable == 'undefined' ? true : false;
}

function in_array(needle, haystack) {
	if(typeof needle == 'string' || typeof needle == 'number') {
		for(var i in haystack) {
			if(haystack[i] == needle) {
					return true;
			}
		}
	}
	return false;
}

function trim(str) {
	return (str + '').replace(/(\s+)$/g, '').replace(/^\s+/g, '');
}

function strlen(str) {
	return (BROWSER.ie && str.indexOf('\n') != -1) ? str.replace(/\r?\n/g, '_').length : str.length;
}

function mb_strlen(str) {
	var len = 0;
	for(var i = 0; i < str.length; i++) {
		len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? (charset == 'utf-8' ? 3 : 2) : 1;
	}
	return len;
}

function mb_cutstr(str, maxlen, dot) {
	var len = 0;
	var ret = '';
	var dot = !dot ? '...' : dot;
	maxlen = maxlen - dot.length;
	for(var i = 0; i < str.length; i++) {
		len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? (charset == 'utf-8' ? 3 : 2) : 1;
		if(len > maxlen) {
			ret += dot;
			break;
		}
		ret += str.substr(i, 1);
	}
	return ret;
}

function preg_replace(search, replace, str, regswitch) {
	var regswitch = !regswitch ? 'ig' : regswitch;
	var len = search.length;
	for(var i = 0; i < len; i++) {
		re = new RegExp(search[i], regswitch);
		str = str.replace(re, typeof replace == 'string' ? replace : (replace[i] ? replace[i] : replace[0]));
	}
	return str;
}

function htmlspecialchars(str) {
	return preg_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], str);
}

function display(id) {
	var obj = $(id);
	if(obj.style.visibility) {
		obj.style.visibility = obj.style.visibility == 'visible' ? 'hidden' : 'visible';
	} else {
		obj.style.display = obj.style.display == '' ? 'none' : '';
	}
}

function checkall(form, prefix, checkall) {
	var checkall = checkall ? checkall : 'chkall';
	count = 0;
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.name && e.name != checkall && !e.disabled && (!prefix || (prefix && e.name.match(prefix)))) {
			e.checked = form.elements[checkall].checked;
			if(e.checked) {
				count++;
			}
		}
	}
	return count;
}

function setcookie(cookieName, cookieValue, seconds, path, domain, secure) {
	var expires = new Date();
	if(cookieValue == '' || seconds < 0) {
		cookieValue = '';
		seconds = -2592000;
	}
	expires.setTime(expires.getTime() + seconds * 1000);
	domain = !domain ? cookiedomain : domain;
	path = !path ? cookiepath : path;
	document.cookie = escape(cookiepre + cookieName) + '=' + escape(cookieValue)
		+ (expires ? '; expires=' + expires.toGMTString() : '')
		+ (path ? '; path=' + path : '/')
		+ (domain ? '; domain=' + domain : '')
		+ (secure ? '; secure' : '');
}

function getcookie(name, nounescape) {
	name = cookiepre + name;
	var cookie_start = document.cookie.indexOf(name);
	var cookie_end = document.cookie.indexOf(";", cookie_start);
	if(cookie_start == -1) {
		return '';
	} else {
		var v = document.cookie.substring(cookie_start + name.length + 1, (cookie_end > cookie_start ? cookie_end : document.cookie.length));
		return !nounescape ? unescape(v) : v;
	}
}