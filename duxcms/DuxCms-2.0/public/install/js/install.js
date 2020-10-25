function no(){
	alert('感谢您对DuxCms的支持！');
	window.close();
}
function showmsg(msg,tyle){
	var html = '<p class="'+tyle+'">'+msg+'</p>';
	$('.ins-log').append(html);
}
function insok(homeUrl,adminUrl){
	var html = '\
    <div class="line-middle">\
        <div class="xm6">\
            <a class="button bg-main button-block text-center" href="'+homeUrl+'">返回首页</a>\
        </div>\
        <div class="xm6">\
            <a class="button bg-sub button-block text-center" href="'+adminUrl+'">进入后台</a>\
        </div>\
    </div>\
	';
	$('.panel-foot').html(html);
}