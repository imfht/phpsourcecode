/*****************************/
/*         底部横栏配置       */
/*****************************/
//有滚动效果，需要在页面加载jQuery以及SuperSlide滚动插件库
swtbottomBannerHTML=
"	<div class=\"sl_swt_bdiv\">"
+"		<input class=\"sl_swt_bkk\" name=\"bottomtel\" type=\"text\" id=\"bottomtel\" onfocus=\"if(this.value==\'点此输入您的号码！\'){this.value=\'\';}\" onblur=\"if(this.value==\'\'){this.value=\'点此输入您的号码！\';}\" onmouseover=\"javascript:this.focus()\"  value=\"点此输入您的号码！\" onkeydown=\"lxbtelkeyUp(event,this)\">"
+"		<a id=\"tijiao\" class=\"sl_swt_btj\" href=\"javascript:;\" onclick=\"lxbtelcall('bottomtel')\" target=\"_self\"></a>"
+"		<a class=\"sl_swt_bswt\" href=\"{swtdir}\" target=\"_blank\" onclick=\"gotoswt(event,this,'pc_swt_bottom');\"></a>"
+"		<div class=\"sl_swt_cdiv\">"
+"			<div class=\"bd\">"
+"				<ul>"
+"					<li><a href=\"{swtdir}\" target=\"_blank\" onclick=\"gotoswt(event,this,'pc_swt_bottom1');\">湖南网友：多动症治疗需要多少钱？</a></li>"
+"					<li><a href=\"{swtdir}\" target=\"_blank\" onclick=\"gotoswt(event,this,'pc_swt_bottom2');\">湖南网友：抽动症怎样治疗最好？</a></li>"
+"					<li><a href=\"{swtdir}\" target=\"_blank\" onclick=\"gotoswt(event,this,'pc_swt_bottom3');\">江浙网友：遗尿症有什么治疗好方法？</a></li>"
+"					<li><a href=\"{swtdir}\" target=\"_blank\" onclick=\"gotoswt(event,this,'pc_swt_bottom4');\">湖南网友：怎么治疗智力低下效果最好？</a></li>"
+"					<li><a href=\"{swtdir}\" target=\"_blank\" onclick=\"gotoswt(event,this,'pc_swt_bottom5');\">安徽网友：自闭症治疗最好的方法是什么？</a></li>"
+"					<li><a href=\"{swtdir}\" target=\"_blank\" onclick=\"gotoswt(event,this,'pc_swt_bottom6');\">浙江网友：怎样治疗遗尿症最省钱？</a></li>"
+"				</ul>"
+"			</div>"
+"		</div>"
+"	</div>"
var swtbottomBanner=document.createElement('div');
swtbottomBanner.id="swtbottomBanner";
swtbottomBanner.innerHTML=swtbottomBannerHTML;
document.body.appendChild(swtbottomBanner);
document.body.style.marginBottom="60px";
//执行滚动函数
$$$(function(){
	jQuery(".sl_swt_cdiv").slide({mainCell:".bd ul",autoPage:true,effect:"topLoop",autoPlay:true,vis:2});
})