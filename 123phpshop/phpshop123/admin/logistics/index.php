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
<?php require_once('../../Connections/localhost.php'); ?>
<?php
mysql_select_db($database_localhost, $localhost);
$query_express_companies = "SELECT * FROM express_company";
$express_companies = mysql_query($query_express_companies, $localhost) or die(mysql_error());
$row_express_companies = mysql_fetch_assoc($express_companies);
$totalRows_express_companies = mysql_num_rows($express_companies);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<p>选择快递公司</p>
<form id="express_companies_form" name="express_companies_form" method="post" action="">
  <p>
    <input type="submit" name="Submit" value="保存" />
  </p>
  <table width="100%" border="1">
    <tr>
      <th scope="col">&nbsp;</th>
      <th scope="col">快递公司</th>
    </tr>
    <?php do { ?>
    <tr>
      <td><input type="checkbox" name="checkbox" value="checkbox" /></td>
      <td><?php echo $row_express_companies['name']; ?></td>
    </tr>
    <?php } while ($row_express_companies = mysql_fetch_assoc($express_companies)); ?>
  </table>
  <p>
    <label></label>
  </p>
</form>
<p>&nbsp;</p>
<p>&nbsp; </p>
</body>
</html>
<?php
mysql_free_result($express_companies);
?>
