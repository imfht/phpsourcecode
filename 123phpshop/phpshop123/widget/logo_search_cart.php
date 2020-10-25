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
<table width="1210" border="0" align="center" style="margin:0px auto;">
  <tr>
    <td width="362" align="left"><a href="/"><img src="<?php echo $row_shopinfo['logo_path']; ?>" width="270" height="60" border="0" /></a></td>
    <td align="center">
 		<form  align="center" id="product_search_form" name="product_search_form" method="get" action="/search.php">
  	 	  <div align="center" style="margin:auto 0;">
  	 	    <input style="float:left;height:24px;line-height:24px;padding:4px;border-style:solid;outline:0;width:446px;border-color:#B61D1D;" name="keywords" type="text" id="keywords" />
  	 	    <input type="submit" style="float:left;padding:6px;width:82px;height:36px;background-color:#B61D1D;color:white;border:none;font-size:16px;cursor:pointer;" name="Submit" value="搜索" />
          </div>
		</form>
    </td>
   <td width="187" height="100" align="right"> 
      <a href="/cart.php" style="text-decoration:none;color:#000000;"><div style="background-color:#F9F9F9;border:1px solid #DFDFDF;text-align:center;height:36px;line-height:36px;width:141px;" cellpadding="0" cellspacing="0">
         购物车&nbsp;&nbsp;&gt;
                </div></a> 
    </td>
  </tr>
</table>
<?php
mysql_free_result($shopinfo);
?>
