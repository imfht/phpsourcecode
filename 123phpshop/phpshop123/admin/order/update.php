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
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE product SET name=%s, ad_text=%s, price=%s, market_price=%s, is_on_sheft=%s, is_hot=%s, is_season=%s, is_recommanded=%s, store_num=%s, intro=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['ad_text'], "text"),
                       GetSQLValueString($_POST['price'], "double"),
                       GetSQLValueString($_POST['market_price'], "double"),
                       GetSQLValueString($_POST['is_on_sheft'], "int"),
                       GetSQLValueString($_POST['is_hot'], "text"),
                       GetSQLValueString($_POST['is_season'], "text"),
                       GetSQLValueString($_POST['is_recommanded'], "text"),
                       GetSQLValueString($_POST['store_num'], "int"),
                       GetSQLValueString($_POST['intro'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());

  $updateGoTo = "../product/index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_product = "-1";
if (isset($_GET['id'])) {
  $colname_product = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_product = sprintf("SELECT * FROM product WHERE id = %s", $colname_product);
$product = mysql_query($query_product, $localhost) or die(mysql_error());
$row_product = mysql_fetch_assoc($product);
$totalRows_product = mysql_num_rows($product);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <p>更新产品信息：</p>
  <table align="center">
    <tr valign="baseline">
      <td nowrap align="right">Name:</td>
      <td><input type="text" name="name" value="<?php echo $row_product['name']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Ad_text:</td>
      <td><input type="text" name="ad_text" value="<?php echo $row_product['ad_text']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Price:</td>
      <td><input type="text" name="price" value="<?php echo $row_product['price']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Market_price:</td>
      <td><input type="text" name="market_price" value="<?php echo $row_product['market_price']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Is_on_sheft:</td>
      <td valign="baseline"><table>
        <tr>
          <td><input type="radio" name="is_on_sheft" value="radiobutton1" <?php if (!(strcmp($row_product['is_on_sheft'],"radiobutton1"))) {echo "CHECKED";} ?>>
            [ 标签 ]</td>
        </tr>
        <tr>
          <td><input type="radio" name="is_on_sheft" value="radiobutton2" <?php if (!(strcmp($row_product['is_on_sheft'],"radiobutton2"))) {echo "CHECKED";} ?>>
            [ 标签 ]</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Is_hot:</td>
      <td valign="baseline"><table>
        <tr>
          <td><input type="radio" name="is_hot" value="radiobutton1" <?php if (!(strcmp($row_product['is_hot'],"radiobutton1"))) {echo "CHECKED";} ?>>
            [ 标签 ]</td>
        </tr>
        <tr>
          <td><input type="radio" name="is_hot" value="radiobutton2" <?php if (!(strcmp($row_product['is_hot'],"radiobutton2"))) {echo "CHECKED";} ?>>
            [ 标签 ]</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Is_season:</td>
      <td valign="baseline"><table>
        <tr>
          <td><input type="radio" name="is_season" value="radiobutton1" <?php if (!(strcmp($row_product['is_season'],"radiobutton1"))) {echo "CHECKED";} ?>>
            [ 标签 ]</td>
        </tr>
        <tr>
          <td><input type="radio" name="is_season" value="radiobutton2" <?php if (!(strcmp($row_product['is_season'],"radiobutton2"))) {echo "CHECKED";} ?>>
            [ 标签 ]</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Is_recommanded:</td>
      <td valign="baseline"><table>
        <tr>
          <td><input type="radio" name="is_recommanded" value="radiobutton1" <?php if (!(strcmp($row_product['is_recommanded'],"radiobutton1"))) {echo "CHECKED";} ?>>
            [ 标签 ]</td>
        </tr>
        <tr>
          <td><input type="radio" name="is_recommanded" value="radiobutton2" <?php if (!(strcmp($row_product['is_recommanded'],"radiobutton2"))) {echo "CHECKED";} ?>>
            [ 标签 ]</td>
        </tr>
      </table></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Store_num:</td>
      <td><input type="text" name="store_num" value="<?php echo $row_product['store_num']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">Intro:</td>
      <td><textarea name="intro" cols="50" rows="5"><?php echo $row_product['intro']; ?></textarea>
      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="更新记录"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
  <input type="hidden" name="id" value="<?php echo $row_product['id']; ?>">
</form>
<p>&nbsp;</p>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>

<script>
$().ready(function(){

	$("#").validate();
	
});</script>
</body>
</html>
<?php
mysql_free_result($product);
?>
