/*
 *	Weiset - tool with javascript
 *  后台Cookie-JS动态配置
 */
var cookie_pre = 'ac_';//cookie前缀
var cookie_domain = '';//作用域名
var cookie_path = '/';//作用区域

//'reload_time',1:刷新页面,0:默认值
function reloadByCookie(name) {
	name = name ? name : 'reload_time';
    var reload_time = getCookie(name);
    if (reload_time == 1) {
        window.location.reload();
    }
}

function getCookie(name) {
    name = cookie_pre+name;
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while(i < clen) {
		var j = i + alen;
		if(document.cookie.substring(i, j) == arg) return getCookieVal(j);
		i = document.cookie.indexOf(" ", i) + 1;
		if(i == 0) break;
	}
	return null;
}

function setCookie(name, value, days) {
    name = cookie_pre+name;
	var argc = setCookie.arguments.length;
	var argv = setCookie.arguments;
	var secure = (argc > 5) ? argv[5] : false;
	var expire = new Date();
	if(days==null || days==0) days=1;
	expire.setTime(expire.getTime() + 3600000*24*days);
	document.cookie = name + "=" + escape(value) + ("; path=" + cookie_path) + ((cookie_domain == '') ? "" : ("; domain=" + cookie_domain)) + ((secure == true) ? "; secure" : "") + ";expires="+expire.toGMTString();
}

function delCookie(name) {
    var exp = new Date();
	exp.setTime (exp.getTime() - 1);
	var cval = getCookie(name);
    name = cookie_pre+name;
	document.cookie = name+"="+cval+";expires="+exp.toGMTString();
}

function getCookieVal(offset) {
	var endstr = document.cookie.indexOf (";", offset);
	if(endstr == -1)
	endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}