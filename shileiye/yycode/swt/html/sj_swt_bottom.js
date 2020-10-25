/*****************************/
/*         手机底部横栏       */
/*****************************/
swtbottomBannerHTML=
'	<ul>'
+'		<li><a href="/"><i></i>首页</a></li>'
+'		<li><a href="/swt/" target="_blank"><i></i>点击咨询</a></li>'
+'		<li><a class="active" href="/swt/?qq" target="_blank"><i></i>专家QQ</a></li>'
+'		<li><a href="tel:073182233632" target="_blank"><i></i>立即通话</a></li>'
+'		<li><a href="/swt/" target="_blank"><i></i>网上挂号</a></li>'
+'	</ul>';
var swtbottomBanner=document.createElement('div');
swtbottomBanner.id="sjswtbottomBanner";
swtbottomBanner.innerHTML=swtbottomBannerHTML;
document.body.appendChild(swtbottomBanner);
document.body.style.marginBottom="64px";