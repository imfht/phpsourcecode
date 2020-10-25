/*****************************/
/*        QQ抖动窗口配置      */
/*****************************/
swtQQBannerHTML=
"	<span class=\"sl_swt_tit\">窗口抖动</span>"
+"	<a class=\"sl_swt_x\" target=\"_self\" href=\"javascript:;\" onclick=\"closeWin(this);return false;\"></a>"
+"	<a class=\"sl_swt_pic\" target=\"_blank\" href=\"{swtdir}/?qq\"><img src=\"{swtdir}/{swtskins}/img/zj_{zjpy}.gif\" /></a>"
+"	<a class=\"sl_swt_qq\" target=\"_blank\" href=\"{swtdir}/?qq\">{zj1}主任（{qq}）</a>"
+"	<span class=\"sl_swt_ts\">发送了一个窗口抖动！</span>"
+"	<a class=\"sl_swt_js\" target=\"_blank\" href=\"{swtdir}/?qq\">接受</a>"
+"	<a class=\"sl_swt_jj\" target=\"_self\" href=\"javascript:;\" onclick=\"closeWin(this);return false;\">拒绝</a>"
var swtQQBanner=document.createElement('div');
swtQQBanner.id="swtQQBanner";
swtQQBanner.innerHTML=swtQQBannerHTML;
document.body.appendChild(swtQQBanner);
$$$(function(){
	qqdoudong();	//执行抖动函数
})
//QQ抖动函数
function qqdoudong(){
	var obj=document.getElementById('swtQQBanner');
	if(obj){
		var posData=[obj.offsetLeft,obj.offsetTop];
		setInterval(function(){
			var i=0;
			clearInterval(timer);
			var timer=setInterval(function(){
				i++;
				obj.style.right=((i%2)>0?-2:2)+'px';
				obj.style.bottom=((i%2)>0?-2:2)+'px';
				if(i>=25){
					clearInterval(timer);
					obj.style.right='0px';
					obj.style.bottom='0px';
				}
			}, 35);	//抖动速度
		},5000);	//抖动间隔
	}
}