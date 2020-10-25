<?php
/**
 * 123PHPSHOP
 * ============================================================================
 * 版权所有 2015 上海序程信息科技有限公司，并保留所有权利。
 * 网站地址: http://www.123PHPSHOP.com；
 * ----------------------------------------------------------------------------
 * 这是一个免费的软件。您可以在商业目的和非商业目的地前提下对程序除本声明之外的
 * 代码进行修改和使用；您可以对程序代码以任何形式任何目的的再发布，但一定请保留
 * 本声明和上海序程信息科技有限公司的联系方式！本软件中使用到的第三方代码版权属
 * 于原公司所有。上海序程信息科技有限公司拥有对本声明和123PHPSHOP软件使用的最终
 * 解释权！
 * ============================================================================
 *  作者:	123PHPSHOP团队
 *  手机:	13391334121
 *  邮箱:	service@123phpshop.com
 */
?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/localhost.php'); ?>
<?php
mysql_select_db($database_localhost, $localhost);
$query_shopinfo = "SELECT logo_path FROM shop_info WHERE id = 1";
$shopinfo = mysql_query($query_shopinfo, $localhost) or die(mysql_error());
$row_shopinfo = mysql_fetch_assoc($shopinfo);
$totalRows_shopinfo = mysql_num_rows($shopinfo);
?>
<table width="100%" height="58" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
  <tr>
    <td>&nbsp;</td>
    <td width="990" valign="middle" ><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="3%"><img   src="<?php echo $row_shopinfo['logo_path']; ?>" height="28" /></td>
          <td width="86%"><strong style="line-height:58px;">收银台</strong></td>
          <td width="11%">&nbsp;</td>
        </tr>
      </table></td>
    <td>&nbsp;</td>
  </tr>
</table>
