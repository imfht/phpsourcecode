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
$query_shopinfo = "SELECT * FROM shop_info WHERE id = 1";
$shopinfo = mysql_query($query_shopinfo, $localhost) or die(mysql_error());
$row_shopinfo = mysql_fetch_assoc($shopinfo);
$totalRows_shopinfo = mysql_num_rows($shopinfo);
?>
<style type="text/css">
<!--
.STYLE1 {
	font-size: 12px;
	color: #ccc;
	line-height:57px;
}
.STYLE3 {font-size: 12px; color: #caecb6;line-height:57px;   }
.STYLE5 {font-size: 12px; color: #7abd54;line-height:57px;  }
.STYLE7 {font-size: 36px}
-->
</style>

<table width="990" border="0" align="center" style="margin:0px auto;margin-top:30px;margin-bottom:10px;">
  <tr>
    <td width="275" height="60" align="left"><div align="left" ><a href="/"><img src="<?php echo $row_shopinfo['logo_path']; ?>" width="275" height="60" border="0" /></a></div></td>
    <td height="60" align="center"> 
      <a href="/cart.php" style="text-decoration:none;color:#000000;"></a> <table width="480" height="60" border="0" align="right" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center" style="height:57px;border-bottom:3px solid #caecb6;"><span class="STYLE3"><span class="STYLE7">1.</span>我的购物车</span></div></td>
          <td><div align="center"  style="height:57px;border-bottom:3px solid #7abd54;"><span class="STYLE5"><span class="STYLE7">2.</span>填写核对订单信息</span></div></td>
          <td><div align="center"  style="height:57px;border-bottom:3px solid #ccc;"><span class="STYLE1"><span class="STYLE7">3.</span>成功提交订单</span></div></td>
        </tr>

      </table></td>
  </tr>
</table>
<?php
mysql_free_result($shopinfo);
?>
