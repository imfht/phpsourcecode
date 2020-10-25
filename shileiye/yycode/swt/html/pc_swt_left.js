/*****************************/
/*         左侧边栏配置       */
/*****************************/
//左侧边栏HTML
swtleftBannerHTML='<a href="javascript:;" target="_self" onclick="closeWin(this);return false;" class="sl_Banner_close"></a><input type="text" id="lefttel" placeholder="输入手机或电话号码" onkeydown="lxbtelkeyUp(event,this)"><a class="teltijiao" href="javascript:;" onclick="lxbtelcall(\'lefttel\');" target="_self"></a><a class="telbeijing" target="_blank" href="{swtdir}" onclick="gotoswt(event,this,\'pc_swt_left\');"></a>';
var swtleftBanner=document.createElement('div');
swtleftBanner.id="swtleftBanner";
swtleftBanner.innerHTML=swtleftBannerHTML;
document.body.appendChild(swtleftBanner);