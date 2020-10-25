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
$query_shop_info = sprintf("SELECT * FROM shop_info WHERE id = 1");
$shop_info = mysql_query($query_shop_info, $localhost) or die(mysql_error());
$row_shop_info = mysql_fetch_assoc($shop_info);
$totalRows_shop_info = mysql_num_rows($shop_info);
?><table width="990" height="60" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="50%" height="60"><table width="275" height="60" border="0">
      <tr>
        <td><a href="../index.php"><img src="<?php echo $row_shop_info['logo_path']; ?>" width="270" height="60" border="0"></a></td>
      </tr>
    </table></td>
    <td height="60"><table width="321" height="60" border="0" align="right">
      <tr bordercolor="0">
        <td width="325">
		<form name="form1" method="get" action="/search.php">
           	<div align="right">
           	  <input style="border:1px solid #c91623;height:24px;width:260px;"  name="key" type="text" id="key">
           	  <input style="border:1px solid #c91623;margin-left:-8px;background:#c91623;width:48px;height:28px;color:#fff;" type="submit" name="Submit" value="搜索">
         	      </div>
		</form>        </td>
      </tr>
    </table></td>
  </tr>
</table>

<?php
mysql_free_result($shop_info);
?>
