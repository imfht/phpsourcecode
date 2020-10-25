/*****************************/
/*        页头菜单栏配置      */
/*****************************/
var swttopBanner_top=158;	//滚动到多少像素显示页头菜单
//页头菜单栏HTML
swttopBannerHTML=
'	<ul class="sl_top_nav">'
+'		<li><a href="/" target="_blank">网站首页</a></li>'
+'		<li><a href="/lanmu/yyjj/" target="_blank" title="医院概况">医院概况</a></li>'
+'		<li><a href="/lanmu/lf/" target="_blank" title="特色疗法">特色疗法</a></li>'
+'		<li><a href="/lanmu/mt/" target="_blank" title="媒体报道">媒体报道</a></li>'
+'		<li><a href="/anli/" target="_blank" title="康复案例">康复案例</a></li>'
+'		<li><a href="/lanmu/zj/" target="_blank" title="专家团队">专家团队</a></li>'
//+'		<li><a href="/shebei/" target="_blank" title="先进设备">先进设备</a></li>'
+'		<li><a href="/lanmu/ry/" target="_blank" title="医院荣誉"> 医院荣誉</a></li>'
+'		<li><a href="/lanmu/lx/" target="_blank" title="来院路线">来院路线</a></li>'
+'	</ul>'

var swttopBanner=document.createElement('div');
swttopBanner.id="swttopBanner";
swttopBanner.innerHTML=swttopBannerHTML;
document.body.appendChild(swttopBanner);

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
//菜单滑动效果，需要加载jQuery
$$$(function() {
	$('#swttopBanner>ul>li').each(function() {
        $(this).hover(function(){
			$('#swttopBanner .top_move_bg').remove();
			var oDiv=$("<div class='top_move_bg'></div>");
			oDiv.insertBefore($(this).children('a'));
			var oMove=$(this).find('.top_move_bg');
			oMove.animate({'left':20},280,function(){
				oMove.animate({'left':0},280);
			})
		},function(){
			$('#swttopBanner .top_move_bg').remove();
		});
    });
})