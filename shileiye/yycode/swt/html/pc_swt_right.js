/*****************************/
/*         PC右侧边栏  	     */
/*****************************/
swtrightBannerHTML='<a href="javascript:;" target="_self" onclick="closeWin(this);return false;" class="sl_Banner_close"></a><a href="{swtdir}" target="_blank" class="sl_swt_pc" onclick="gotoswt(event,this,\'pc_swt_right\');"></a>';
var swtrightBanner=document.createElement('div');
swtrightBanner.id="swtrightBanner";
swtrightBanner.innerHTML=swtrightBannerHTML;
document.body.appendChild(swtrightBanner);