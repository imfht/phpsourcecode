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
<?php require_once('../Connections/localhost.php'); ?>
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
 	 
	 if(!empty($_FILES['logo_path']['name'])){
	 
		include($_SERVER['DOCUMENT_ROOT'].'/Connections/lib/upload.php'); 
	  
		$up = new fileupload;
		//设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
		$up -> set("path", $_SERVER['DOCUMENT_ROOT']."/uploads/product/");
		$up -> set("maxsize", 2000000);
		$up -> set("allowtype", array("gif", "png", "jpg","jpeg"));
		$up -> set("israndname", true);
	  
		//使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
		if($up->upload("logo_path")) {
		   $logo_path="/uploads/product/".$up->getFileName(); 
		   
			 $updateSQL = sprintf("UPDATE shop_info SET name=%s, email=%s, mobile=%s, province=%s, city=%s, district=%s, address=%s, zip=%s, logo_path=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['mobile'], "text"),
                       GetSQLValueString($_POST['province'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['district'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString($logo_path, "text"),
                       GetSQLValueString(1, "int"));
 		}else {
 			echo '<pre>';
			//获取上传失败以后的错误提示
			var_dump($up->getErrorMsg());
			echo '</pre>';
		}
	 }else{
	 
	 	$updateSQL = sprintf("UPDATE shop_info SET name=%s, email=%s, mobile=%s, province=%s, city=%s, district=%s, address=%s, zip=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['mobile'], "text"),
                       GetSQLValueString($_POST['province'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['district'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString(1, "int"));
 	 }
	 
	  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());
  
 	// 我们这里需要对上传文件进行检查
 }
 
mysql_select_db($database_localhost, $localhost);
$query_info = "SELECT * FROM shop_info WHERE id = 1";
$info = mysql_query($query_info, $localhost) or die(mysql_error());
$row_info = mysql_fetch_assoc($info);
$totalRows_info = mysql_num_rows($info);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">店铺信息</p>

    <form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1">
      <table align="center" class="phpshop123_form_box">
        <tr valign="baseline">
          <td nowrap align="right">名称:</td>
          <td><input name="name" type="text" value="<?php echo isset($row_info['name'])?$row_info['name']:''; ?>" size="32" maxlength="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">邮件:</td>
          <td><input name="email" type="text" value="<?php echo isset($row_info['email'])?$row_info['email']:''; ?>" size="32" maxlength="50"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">手机:</td>
          <td><input name="mobile" type="text" value="<?php echo isset($row_info['mobile'])?$row_info['mobile']:''; ?>" size="32" maxlength="11"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">省市:</td>
          <td><?php include($_SERVER['DOCUMENT_ROOT'].'/widget/area/index.php');?></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">地址:</td>
          <td><input name="address" type="text" value="<?php echo isset($row_info['address'])?$row_info['address']:""; ?>" size="32" maxlength="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">邮编:</td>
          <td><input name="zip" type="text" value="<?php echo isset($row_info['zip'])?$row_info['zip']:""; ?>" size="32" maxlength="6"></td>
        </tr>
		
        <tr valign="baseline">
          <td nowrap align="right">Logo</td>
          <td><img src="<?php echo $row_info['logo_path']; ?>" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">更新Logo:</td>
          <td><input type="file" name="logo_path" value="<?php echo isset($row_info['logo_path'])?$row_info['logo_path']:""; ?>" size="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" value="更新记录"></td>
        </tr>
      </table>
      <input type="hidden" name="MM_update" value="form1">
     </form>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/widget/area/jsAddress.js"></script>
<script>
addressInit('province', 'city', 'district', '<?php echo $row_info['province']; ?>', '<?php echo $row_info['city']; ?>', '<?php echo $row_info['district']; ?>');
</script>
</body>

</html>
<?php
mysql_free_result($info);
?>
