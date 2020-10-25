/*
自定义离线宝API V1.0
作者：shileiye
时间：2015.6.20
说明：
1、兼容官方离线宝API格式，可使用lxb.call()调用。
2、增加监听离线宝回车键提交(给电话输入框加上onkeydown="lxbtelkeyUp(event,this)"属性可实现回车键提交)
3、增加离线宝点击提交(给点击提交元素加上onclick="lxbtelcall('tel');"其中tel为电话输入框ID)
4、使用API不需要from提交。
*/
var bdlxb={};
if(!typeof(info)=="undefined"){
	bdlxb.uid=info['lxbuid'];	//如果使用了YYCODE则优先使用其配置的uid
}else{
	//获取<script>上第一个设置有data-lxb-uid的值
	scripts=document.getElementsByTagName("script");
	for (var i=0;scripts.length>i;i++) {
		if (scripts[i].getAttribute("data-lxb-uid")) {
			bdlxb.uid=scripts[i].getAttribute("data-lxb-uid");
			break
		}
	}
}
bdlxb.host='http://lxbjs.baidu.com';
bdlxb.key='';
bdlxb.tel='';
//获取离线宝JSON数据
function getlxb(url,callback) {
	var script=document.createElement('script');
	script.setAttribute('type','text/javascript');
	script.setAttribute('src',url+"&callback="+callback);
	document.body.appendChild(script);
}
//获取KEY
function keycallback(s){
	if (0 == s.status) {
		bdlxb.key = s.data.tk;	//KEY赋值到变量
		tjtel(bdlxb.tel);
	}
}
//获取提交提示
function tjcallback(s){
	alert(s.msg);
}
//电话号码验证
function validateTel(str) {
	var res = true;
	if (str.charAt(0) == '1') {
		res = /^1[34578]\d{9}$/.test(str);
	} else {
		res = /^0\d{9,11}$/.test(str);
	}
	return res;
}
//获取KEY
function getkey(o){
	if (!o) {
		alert('在页面中找不到输入框' + o);
		return;
	}
	var vtel = o.value;
	if (!validateTel(vtel)) {
		alert('请您输入正确的号码，手机号码请直接输入，座机请加区号', true);
		return false;
	}
	bdlxb.tel=o;
	//如果没有KEY
	if (bdlxb.key == ''){
		var url = bdlxb.host + '/cb/user/check';
		url += '?f=4&uid=' + (bdlxb.uid ? bdlxb.uid:'');
		url += '&r=' + encodeURIComponent((document.referer ? document.referer: location.href));
		url += '&t=' + (new Date()).getTime();
		getlxb(url,'keycallback');
	} else {
		tjtel(o);
	}
}
//提交号码
function tjtel(o) {
	var url = bdlxb.host + '/cb/call';
	url += '?vtel=' + o.value;
	url += '&uid=' + (bdlxb.uid ? bdlxb.uid:'');
	url += '&tk=' + bdlxb.key;
	url += '&t=' + (new Date()).getTime();
	getlxb(url,'tjcallback');
}
if (!window.lxb) {
	window['lxb'] = {};
}
window['lxb']['call'] = getkey;
//监听离线宝回车键提交(给电话输入框加上onkeydown="lxbtelkeyUp(event,this)"属性可实现回车键提交)
function lxbtelkeyUp(e,o) {
	var e=(e)?e:((window.event)?window.event:"");
	var currKey=e.keyCode?e.keyCode:e.which;
	if(currKey==13){lxb.call(o);o.focus();};
}

//离线宝点击提交(给点击提交元素加上onclick="lxbtelcall('tel');"其中tel为电话输入框ID)
function lxbtelcall(o){
	var o=document.getElementById(o);
	lxb.call(o);
	o.focus();
}