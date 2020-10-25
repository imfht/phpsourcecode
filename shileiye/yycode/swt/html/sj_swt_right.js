/*****************************/
/*         手机右侧广告      */
/*****************************/
swtrightBannerHTML='<a target="_blank" href="{dhurl}" class="sl_swt_sj" onclick="gotoswt(event,this,\'sj_swt_right\');"></a>';
var swtrightBanner=document.createElement('div');
swtrightBanner.id="swtrightBanner";
swtrightBanner.innerHTML=swtrightBannerHTML;
document.body.appendChild(swtrightBanner);