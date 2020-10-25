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
<?php require_once('../../../Connections/localhost.php'); ?>
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

mysql_select_db($database_localhost, $localhost);
$query_shipping_method = sprintf("SELECT * FROM shipping_method WHERE config_file_path = 'ems'");
$shipping_method = mysql_query($query_shipping_method, $localhost) or die(mysql_error());
$row_shipping_method = mysql_fetch_assoc($shipping_method);
$totalRows_shipping_method = mysql_num_rows($shipping_method);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO shipping_method_area (name, shipping_method_id, shipping_by_quantity, single_product_fee, half_kg_fee, continue_half_kg_fee, area) VALUES (%s, %s, %s, %s, %s,  %s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($row_shipping_method['id'], "int"),
                       GetSQLValueString($_POST['shipping_by_quantity'], "int"),
                       GetSQLValueString($_POST['single_product_fee'], "double"),
                       GetSQLValueString($_POST['half_kg_fee'], "double"),
                       GetSQLValueString($_POST['continue_half_kg_fee'], "double"),
                        GetSQLValueString($_POST['area'], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
  
    $insertGoTo = "/admin/shipping_method_area/index.php?shipping_method_id=".$row_shipping_method['id'];
   header(sprintf("Location: %s", $insertGoTo));
   
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">EMS:新建配送区域</p>
<form method="post" name="form1" id="form1" action="<?php echo $editFormAction; ?>">
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">名称:</td>
      <td><input name="name" type="text" value="" size="32" maxlength="32">
*</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">运费计算:</td>
      <td valign="baseline"><input name="shipping_by_quantity" type="radio" value="0" checked="checked" onchange="by_weight()" />
按重量
  <input type="radio" name="shipping_by_quantity" value="1" onchange="by_quantity()" />
按数量</td>
    </tr>
    <tr valign="baseline" class="by_quantity" style="display:none;">
      <td nowrap align="right">单件商品费用:</td>
      <td><input name="single_product_fee" type="text" value="" size="32" maxlength="32">
*</td>
    </tr>
    <tr valign="baseline" class="by_weight">
      <td nowrap align="right">500克之内费用:</td>
      <td><input name="half_kg_fee" type="text" value="" size="32" maxlength="32">
*</td>
    </tr>
    <tr valign="baseline" class="by_weight">
      <td nowrap align="right">续费500克费用:</td>
      <td><input name="continue_half_kg_fee" type="text" value="" size="32" maxlength="32">
*</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">区域：</td>
      <td><?php include_once($_SERVER['DOCUMENT_ROOT'].'/admin/widgets/location_sel.php');?></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="添加"></td>
    </tr>
  </table>
   <input type="hidden" name="area" value="">
  <input type="hidden" name="MM_insert" value="form1">
</form>
<script language="JavaScript" type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/shipping_method.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script>
$().ready(function(){

	$("#form1").validate({
        rules: {
            name: {
                required: true
            },
			half_kg_fee: {
                required: true,
				number:true
				  
            },
			continue_half_kg_fee: {
                required: true,
				number:true
				  
            },
            single_product_fee: {
                required: true,
				number:true
				  
            },
            half_kg_fee: {
                required: true,
				number:true
            } ,
            free_quota: {
                number:true
            }
        },
        messages: {
            name: {
                required: "必填" 
            },
			half_kg_fee: {
                required: "必填" ,
				 number:"必须是数字哦"
				  
            },
			continue_half_kg_fee: {
                required: "必填" ,
				 number:"必须是数字哦"
				  
            },
            single_product_fee: {
               required: "必填" ,
			   number:"必须是数字哦"
				  
            },
            half_kg_fee: {
                required: "必填" ,
				 number:"必须是数字哦"
            } ,
            free_quota: {
				number:"必须是数字哦"
             }
        }
    });
	
});</script>
</body>
</html>
