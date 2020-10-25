//umeditor 根目录，解决dialog路径问题。
window.UMEDITOR_HOME_URL = 'inc/umeditor/';

//自动运行函数
var interval_id = null;
function auto_run(fun, inter) {
	auto_run_stop();
	interval_id = setInterval(fun, inter);
}	
function auto_run_stop() {
	clearInterval(interval_id); 
}
//ajax 钩子
var ajax_is_send = false;
function ajax_start(area) {
	if (ajax_is_send) return false;
	$(area).append('<center><img id="loading_img" src="img/loading_img.jpg" height="70px"/></center>');	
	ajax_is_send = true;
	return true; 
}
function ajax_stop() {
	$('img#loading_img').remove();
	ajax_is_send = false;
}

//cookie 操作
function setCookie(c_name,value,expiredays,path) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+
		((expiredays==null) ? "" : ";expires="+exdate.toGMTString())+
		((path==null) ? "" : ";path="+path);
}
function getCookie(c_name) {
	if (document.cookie.length>0) {
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1) { 
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end));
		} 
	}
	return ""
}
//登录回跳
function jump_to_login(back_url) {
	setCookie('login_jump', back_url, 365, '/');
	location = '?c=user&a=login';
}
//获取邮箱激活码
function get_email_code() {
	$.get('?c=user&a=do_send_email_code&email='+$('input#email').val(), function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		}
		alert('激活码已经发往您的邮箱，请注意查收！^_^');
	});
}
//加密密码
document.write('<script src="js/vendor/webtoolkit.sha1.js"></script>');
function encrypt_password() {
	$('input#password').val(SHA1($('input#password').val()));
}
//幻灯片自适应
function auto_c_h(propor) {
	if (900 < $('div.container').width()) return;
	var c_h = ($('div.carousel-inner').find('div.item').find('a').find('div').width()*propor)+'px'; 
	$('div.carousel-inner').find('div.item').find('a').find('div').css('height', c_h);
	$('div.carousel-inner').find('div.item').find('a').find('div').css('background-size', 'auto '+c_h);
	$('ol.carousel-indicators').remove();
}
//默认无法加载的图片
$(document).ready(function(){ $('img').each(function(){ if (true == this.complete && 0 == this.naturalWidth) this.src='img/center/d_u_face.jpg'; }); });
$(document).ajaxComplete(function(){ $('img').error(function(){ this.src='img/center/d_u_face.jpg'; }); });

//超过范围的元素百分百宽度
img_load();
$(document).ajaxComplete(function(){ img_load(); });
function img_load() {
	$('div.text-content').find('*').each(function(){ img_resize($(this)); });	//text-content内宽高调整
	$('div.text-content').find('*').load(function(){ img_resize($(this)); });	//后加载元素
}
function img_resize(t) {
	var t_w = t.width();
	var t_h = t.height();
	var p_w = t.parents('div.text-content').width();
	if (t_w > p_w) { 
		t.css('width', p_w+'px'); if (t[0].tagName != 'VIDEO') t.css('height', t_h*(p_w/t_w)+'px'); 
	}
}
function get_content_file(f_url) {
	$.get(f_url, function(rs){
		$('div.text-content').append(rs);
	});
}
//防盗链图片加载
if (null == location.href.match(/topic_edit/)) {
	$('iframe').each(function(i, e){
		if ($(e).attr('src_d')) {
			var f_id = Math.random();

			window.src_d = '<script src="//libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>';
			window.src_d += '<img id="f_i" src=\''+$(e).attr('src_d')+'\' onload="setTimeout(function(){ var p_f = $(parent.frames[\''+f_id+'\']); p_f.after(\'<img style=max-width:100% src='+$(e).attr('src_d')+'>\'); p_f.remove();}, 900)"/>';

			$(e).attr('id', f_id);
			$(e).attr('src', 'javascript:parent.src_d');
		}
	});
}
