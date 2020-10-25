/*****************************/
/*         手机左侧边栏       */
/*****************************/
//左侧边栏HTML
swtleftBannerHTML='<a target="_blank" href="{swtdir}" id="sjswtLeftBanner" onclick="gotoswt(event,this,\'sj_swt_left\');">在线咨询</a>';
var swtleftBanner=document.createElement('div');
swtleftBanner.id="swtleftBanner";
swtleftBanner.innerHTML=swtleftBannerHTML;
document.body.appendChild(swtleftBanner);