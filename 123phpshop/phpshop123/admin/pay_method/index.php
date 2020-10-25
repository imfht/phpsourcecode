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
$query_pay_methods = "SELECT * FROM pay_method ORDER BY is_activated DESC";
$pay_methods = mysql_query($query_pay_methods, $localhost) or die(mysql_error());
$row_pay_methods = mysql_fetch_assoc($pay_methods);
$totalRows_pay_methods = mysql_num_rows($pay_methods);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">支付方式列表</p>
<?php if ($totalRows_pay_methods == 0) { // Show if recordset empty ?>
  <p><a href="add.php">添加支付方式</a></p>
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_pay_methods > 0) { // Show if recordset not empty ?>
  <table width="100%" border="1" class="phpshop123_list_box">
    <tr>
      <th scope="col">名称</th>
      <th scope="col">官网</th>
      <th scope="col">介绍</th>
      <th scope="col">状态</th>
      <th scope="col">操作</th>
    </tr>
    <?php do { ?>
      <tr>
        <td><a href="<?php echo $row_pay_methods['folder']; ?>"><?php echo $row_pay_methods['name']; ?></a></td>
        <td><a href="<?php echo $row_pay_methods['www']; ?>"><?php echo $row_pay_methods['www']; ?></a></td>
        <td><?php echo $row_pay_methods['intro']; ?></td>
        <td><div align="right"><?php echo $row_pay_methods['is_activated']?"已激活":"未激活"; ?></div></td>
        <td>
		  <div align="right">
		    <?php if($row_pay_methods['is_activated']==0){ ?>
		      <a href="activate.php?id=<?php echo $row_pay_methods['id']; ?>">激活</a>
	        <?php }else{ ?>
		      <a href="deactivate.php?id=<?php echo $row_pay_methods['id']; ?>">失效</a>
	        <?php }?> <a href="update.php?id=<?php echo $row_pay_methods['id']; ?>">编辑</a></div></td>
      </tr>
      <?php } while ($row_pay_methods = mysql_fetch_assoc($pay_methods)); ?>
  </table>
    <?php } // Show if recordset not empty ?><p>&nbsp; </p>
</body>
</html>
<?php
mysql_free_result($pay_methods);
?>
