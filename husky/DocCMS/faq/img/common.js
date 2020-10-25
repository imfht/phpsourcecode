/*code by 氓流果 www.weibo.com/chnhou*/
var leftmenu='';
//第1个栏目开始
leftmenu+='<li class="menu"><ul>';
leftmenu+='<li class="button"><a href="index.html">@标签一览表</a></li>';
leftmenu+='</ul></li>';
//第3个栏目开始
leftmenu+='<li class="menu"><ul>';
leftmenu+='<li class="button"><a href="#">@模板标题、SEO标签</a></li>';
leftmenu+='<li class="dropdown"><ul id="seolabel">';
leftmenu+='<li><a href="#" rel="seotitle.html">$tag[\'seo.title\']</a></li>';
leftmenu+='<li><a href="#" rel="seokeywords.html">$tag[\'seo.keywords\']</a></li>';
leftmenu+='<li><a href="#" rel="seodescription.html">$tag[\'seo.description\']</a></li>';
leftmenu+='<li><a href="#" rel="title.html">$tag[\'title\']</a></li>';
leftmenu+='<li><a href="#" rel="keywords.html">$tag[\'keywords\']</a></li>';
leftmenu+='<li><a href="#" rel="description.html">$tag[\'description\']</a></li>';
leftmenu+='<li><a href="#" rel="sitetitle.html">$tag[\'site.title\']</a></li>';
leftmenu+='<li><a href="#" rel="sitekeywords.html">$tag[\'site.keywords\']</a></li>';
leftmenu+='<li><a href="#" rel="sitedescription.html">$tag[\'site.description\']</a></li>';
leftmenu+='</ul></li></ul></li>';
//第4个栏目开始
leftmenu+='<li class="menu"><ul>';
leftmenu+='<li class="button"><a href="#">@模板导航、子导航标签</a></li>';
leftmenu+='<li class="dropdown"><ul id="navlabel">';
leftmenu+='<li><a href="#" rel="nav_main.html">nav_main</a></li>';
leftmenu+='<li><a href="#" rel="nav_sub.html">nav_sub</a></li>';
leftmenu+='<li><a href="#" rel="nav_custom.html">nav_custom</a></li>';
leftmenu+='<li><a href="#" rel="nav_location.html">nav_location</a></li>';
leftmenu+='</ul></li></ul></li>';
//第5个栏目开始
leftmenu+='<li class="menu"><ul>';
leftmenu+='<li class="button"><a href="#">@模块应用、调用标签</a></li>';
leftmenu+='<li class="dropdown"><ul id="doclabel">';
leftmenu+='<li><a href="#" rel="doc_article.html">doc_article</a></li>';
leftmenu+='<li><a href="#" rel="doc_download.html">doc_download</a></li>';
leftmenu+='<li><a href="#" rel="doc_focus.html">doc_focus</a></li>';
leftmenu+='<li><a href="#" rel="doc_guestbook.html">doc_guestbook</a></li>';
leftmenu+='<li><a href="#" rel="doc_jobs.html">doc_jobs</a></li>';
leftmenu+='<li><a href="#" rel="doc_linkers.html">doc_linkers</a></li>';
leftmenu+='<li><a href="#" rel="doc_list.html">doc_list</a></li>';
leftmenu+='<li><a href="#" rel="doc_mapshow.html">doc_mapshow</a></li>';
leftmenu+='<li><a href="#" rel="doc_picture.html">doc_picture</a></li>';
leftmenu+='<li><a href="#" rel="doc_poll.html">doc_poll</a></li>';
leftmenu+='<li><a href="#" rel="doc_product.html">doc_product</a></li>';
leftmenu+='<li><a href="#" rel="doc_rss.html">doc_rss</a></li>';
leftmenu+='<li><a href="#" rel="doc_user.html">doc_user</a></li>';
leftmenu+='<li><a href="#" rel="doc_video.html">doc_video</a></li>';
leftmenu+='<li><a href="#" rel="doc_webmap.html">doc_webmap</a></li>';
leftmenu+='</ul></li></ul></li>';
//第6个栏目开始
leftmenu+='<li class="menu"><ul>';
leftmenu+='<li class="button"><a href="#">@系统应用、功能标签</a></li>';
leftmenu+='<li class="dropdown"><ul id="syslabel">';
leftmenu+='<li><a href="#" rel="sys_push.html">sys_push</a></li>';
leftmenu+='<li><a href="#" rel="sys_push_one.html">sys_push_one</a></li>';
leftmenu+='<li><a href="#" rel="sys_about.html">sys_about</a></li>';
leftmenu+='<li><a href="#" rel="sys_mail.html">sys_mail</a></li>';
leftmenu+='<li><a href="#" rel="sys_counts.html">sys_counts</a></li>';
leftmenu+='<li><a href="#" rel="sys_menu_info.html">sys_menu_info</a></li>';
leftmenu+='<li><a href="#" rel="sys_href.html">sys_href</a></li>';
leftmenu+='<li><a href="#" rel="sys_parts.html">sys_parts</a></li>';
leftmenu+='<li><a href="#" rel="sys_substr.html">sys_substr</a></li>';
leftmenu+='<li><a href="#" rel="sys_get_session.html">sys_get_session</a></li>';
leftmenu+='<li><a href="#" rel="sys_set_session.html">sys_set_session</a></li>';
leftmenu+='</ul></li></ul></li>';
//第6个栏目开始
leftmenu+='<li class="menu"><ul>';
leftmenu+='<li class="button"><a href="#">@系统路径、常量输出标签</a></li>';
leftmenu+='<li class="dropdown"><ul id="taglabel">';
leftmenu+='<li><a href="#" rel="siteurl.html">$tag[\'site.url\'] </a></li>';
leftmenu+='<li><a href="#" rel="pathroot.html">$tag[\'path.root\']</a></li>';
leftmenu+='<li><a href="#" rel="pathskin.html">$tag[\'path.skin\'] </a></li>';
leftmenu+='<li><a href="#" rel="formactionsearch.html">$tag[\'form.action.search\'] </a></li>';
leftmenu+='</ul></li></ul></li>';
//页面加载完成
$(document).ready(function(){
	$(".subnav").html(leftmenu);
	var arrayUrl=window.location.href.split("/");
	var current=arrayUrl[arrayUrl.length-1].replace("#","");
	var currfolder=arrayUrl[arrayUrl.length-2];
	var folder=currfolder.indexOf("label")>-1?"../":"";
	$("#"+currfolder).length>0?$("#"+currfolder).parent().show():"";
	$(".subnav a").each(function(){
		getpid=$(this).parent().parent().attr("id");
		if(typeof(getpid)!="undefined"){
			$(this).attr("href",folder+getpid+"/"+$(this).attr("rel"));
		}else{
			($(this).attr("href")!="#")?$(this).attr("href",folder+$(this).attr("href")):'';
		}
		if($(this).attr("rel")==current){
			$(this).attr("class","selected");
		}
	});
	/*左侧菜单弹动效果*/
	$.easing.def = "easeOutBounce";
	$('li.button a').click(function(e){
		var dropDown = $(this).parent().next();
		$('.dropdown').not(dropDown).slideUp('slow');
		dropDown.slideToggle('slow');
		e.preventDefault();
		($(this).attr("href")!="#")?window.location.href=$(this).attr("href"):'';
	})//
	//
	$(".index li").each(function(){
		$(this).hover(function () {
			$(this).addClass("hover");
		},
		function () {
			$(this).removeClass("hover");
		});
	});
});
var _docProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("<script src='" + _docProtocol + "www.doccms.com/skins/doccms/js/tips.js' type='text/javascript'></script>"));