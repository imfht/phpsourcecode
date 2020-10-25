<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:8px;" class="MainTable">
  <tr>
    <td class="about"><? //echo Display_Footer();?></td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
  <tr>
    <td class="foot">联系邮箱：
        <? //=Application(SiteID & "_Ok3w_SiteEmail")?>-在线QQ：
        <? //=Application(SiteID & "_Ok3w_SiteQQ")?><br /><a href="http://www.miibeian.gov.cn/" target="_blank">
        <? //=Application(SiteID & "_Ok3w_SiteTCPIP")?></a>
        <br />Copyright <? //=Year(Date())?>, 版权所有
        <? //=Replace(Application(SiteID & "_Ok3w_SiteUrl"),"http://","")?>.<br />
        <? //Call Ok3w_Site_Tongji()?>
    </td>
  </tr>
</table>