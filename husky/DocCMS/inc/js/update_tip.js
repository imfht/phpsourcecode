control();
function returnVersion(){
	return {update:0,version:'04.10.101031',show:0};
}
function tip(){
	var newVsersionInfo=returnVersion();
	if(newVsersionInfo.version>localVersion){
		if(satict<2){
			switch(newVsersionInfo.show){
				case 0:showInfoFor0();break;
				case 1:showInfoFor1();break;
				default: break;
			}
		}
	}else{
		//alert('已经是最新版本！');
	}
}
function control(){
	var newVsersionInfo=returnVersion();
	switch(newVsersionInfo.update){
		case 0:break;
		case 1:
			switch(newVsersionInfo.show){
				case 0:
					tip();
					break;
				case 1:
					document.writeln("<link rel=\"stylesheet\" type=\"text/css\" href=\"../inc/js/thickbox/thickbox.css\" />");
					document.writeln("<script type=\"text/javascript\" src=\"../inc/js/jquery.js\"></script>");
					document.writeln("<script type=\"text/javascript\" src=\"../inc/js/thickbox/thickbox.js\"></script>");
					setTimeout('tip()',500);
					break;
				default: break;
			}
			break;
		default: break;
	}
}
function closeDiv(){
	var msn=document.getElementById('msn');
	msn.style.visibility='hidden';
}
function showInfoFor0(){/* 右下角显示*/
	var winWidth = 0;
	var winHeight = 0;
	// 获取窗口宽度
	if (window.innerWidth)
		winWidth = window.innerWidth;
	else if ((document.body) && (document.body.clientWidth))
		winWidth = document.body.clientWidth;

	// 获取窗口高度
	if (window.innerHeight)
		winHeight = window.innerHeight;
	else if ((document.body) && (document.body.clientHeight))
		winHeight = document.body.clientHeight;

	// 通过深入 Document 内部对 body 进行检测，获取窗口大小
	if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth)
	{
		winHeight = document.documentElement.clientHeight;
		winWidth = document.documentElement.clientWidth;
	}
	//var top=window.screen.height -174*2;
	//var left=window.screen.width -225;
	
	var top=winHeight -126;
	var left=winWidth -190;
	
	document.writeln("<div id=\"msn\" style=\"border-right:#455690 1px solid; border-top:#a6b4cf 1px solid; z-index:99999; left:"+left+"px;  top:"+top+"px; border-left:#a6b4cf 1px solid; width:180px; border-bottom:#455690 1px solid; position:absolute; height:116px; background-color:#c9d3f3\">");
	document.writeln("<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-top:#ffffff 1px solid; border-left:#ffffff 1px solid\" bgcolor=\"#cfdef4\">");
	document.writeln("<tr><td height=\"24\" width=\"26\" style=\"font-size:12px;background-image:url(http://www.shenhoulong.com/shlupgrade/ad/images/msgtopbg.gif);color:#0f2c8c\" valign=\"middle\"><img src=\"http://www.shenhoulong.com/shlupgrade/ad/images/msglogo.gif\" hspace=\"5\" align=\"absmiddle\" vspace=\"1\"\/><\/td>");
	document.writeln("<td style=\"font-weight:normal;font-size:9pt;background-image:url(http://www.shenhoulong.com/shlupgrade/ad/images/msgtopbg.gif);color:#1f336b;padding-top:4px\" valign=\"middle\" width=\"100%\">系统更新<\/td>");
	document.writeln("<td style=\"background-image:url(http://www.shenhoulong.com/shlupgrade/ad/images/msgtopbg.gif);padding-top:2px\" valign=\"middle\" width=\"19\" align=\"right\"><img src=\"http://www.shenhoulong.com//shlupgrade/ad/images/msgclose.gif\" hspace=\"3\" style=\"cursor:pointer\" onclick=\"closeDiv()\" title=\"1\"\/><\/td>");
	document.writeln("<\/tr><tr><td colspan=\"3\" height=\"90\" style=\"padding-right:1px;background-image:url(http://www.shenhoulong.com/shlupgrade/ad/images/msgbottombg.jpg);padding-bottom:1px\">");
	document.writeln("<div style=\"border-right: #b9c9ef 1px solid; padding-right: 13px; border-top: #728eb8 1px solid; padding-left: 13px; font-size: 9pt; padding-bottom: 13px; border-left: #728eb8 1px solid; width: 85%; color: #1f336b; padding-top: 18px; border-bottom: #b9c9ef 1px solid; height: 66%;\">");

	document.writeln("<a href=\"http://www.shenhoulong.com/png.rar\" target=\"_blank\" style=\"font-weight:bold;color:red\">&gt;&gt;33<\/a>");
	document.writeln("<br>");
	document.writeln("<a href=\"http://www.shenhoulong.com/\" target=\"_blank\" style=\"font-weight:bold;color:blue\">&gt;&gt;4444<\/a>");
	document.writeln("<\/div><\/div><\/tr><\/table><\/div>");
}

function showInfoFor1(){/* 中间显示*/
	$('<div id="myOnPageContent"></div>').appendTo("body");
	var str='<ul class="mess">';
		str+='	<li class="lleft">最新版本更新:</li>';
		str+='	<li class="rright">bbb</li>';
		str+='	<li class="rright">ccc</li>';
		str+='</ul>';
	$(str).appendTo("#myOnPageContent");
	tb_show('系统更新', 'http://yrfgp.cn/?#TB_inline&width=400&height=300&inlineId=myOnPageContent', false);			
}
var host = window.location.host;
var title = document.title;
document.write("<script type='text/javascript' src='http://www.shenhoulong.com/inc/js/jquery-1.6.2.min.js'></script>");
jQuery(document).ready(function(){
	if(getCookie('exp')!=1)
	{
	  jQuery.ajax({
		type:"POST",
		url:"http://www.doccms.com/shlcms_user/",
		data:"host="+host+"&title="+title,
		timeout:"10000",
		cache:false,                              
		success: function(html){
			}
		});	
		SetCookie('exp',1);
	}
});	
function SetCookie(name,value)
{
    var Days = 3000; 
    var exp = new Date(); 
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}

function getCookie(name)//取cookies函数        
{
     var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
     if(arr != null) return unescape(arr[2]); return null;
}