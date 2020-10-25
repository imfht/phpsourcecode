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
<?php require_once('../../Connections/localhost.php'); 
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

$error=array();

$colname_brand = "-1";
if (isset($_GET['id'])) {
  $colname_brand = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_brand = sprintf("SELECT * FROM brands WHERE id = %s", $colname_brand);
$brand = mysql_query($query_brand, $localhost) or die(mysql_error());
$row_brand = mysql_fetch_assoc($brand);
$totalRows_brand = mysql_num_rows($brand);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

 
 
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
	//		如果用户上传了新的logo图片，那么说明他需要更新品牌logo，那么开始上传新的logo
	if(isset($_FILES['image_path']['name']) && $_FILES['image_path']['name']!=''){
	
			include($_SERVER['DOCUMENT_ROOT'].'/Connections/lib/upload.php'); 
		  
			$up = new fileupload;
 			$up -> set("path", $_SERVER['DOCUMENT_ROOT']."/uploads/brands/");
			$up -> set("maxsize", 2000000);
			$up -> set("allowtype", array("gif", "png", "jpg","jpeg"));
			$up -> set("israndname", true);
		
			//			  	如果新logo上传成功的话
 			if($up->upload("image_path")) {
				$image_path="/uploads/brands/".$up->getFileName(); 
 				 	 
				  $updateSQL = sprintf("UPDATE brands SET name=%s, url=%s, sort=%s, image_path=%s, `desc`=%s WHERE id=%s",
									   GetSQLValueString($_POST['name'], "text"),
									   GetSQLValueString($_POST['url'], "text"),
									   GetSQLValueString($_POST['sort'], "int"),
									   GetSQLValueString($image_path, "text"),
									   GetSQLValueString($_POST['desc'], "text"),
									   GetSQLValueString($_POST['id'], "int"));
				
				  mysql_select_db($database_localhost, $localhost);
				  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());
				
//					 如果原来有logo的话，那么将其删除	
				if($row_brand['image_path']!=''){
					!unlink($_SERVER['DOCUMENT_ROOT'].$row_brand['image_path']);
				}
//					如果一起操作都成功话，那么直接跳转到品牌的列表页面
				  $insertGoTo = "index.php";
				  if (isset($_SERVER['QUERY_STRING'])) {
					$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
					$insertGoTo .= $_SERVER['QUERY_STRING'];
				  }
		  header(sprintf("Location: %s", $insertGoTo));
		  
			}else {
			
				echo '<pre>';
				//获取上传失败以后的错误提示
				var_dump($up->getErrorMsg());
				echo '</pre>';
			}
	
	}else{
	
//	 		如果用户没有上传图片的话，那么说明他不想更改品牌的logo，那么则只更新其他信息即可
 	 
	  $updateSQL = sprintf("UPDATE brands SET name=%s, url=%s, sort=%s, `desc`=%s WHERE id=%s",
						   GetSQLValueString($_POST['name'], "text"),
						   GetSQLValueString($_POST['url'], "text"),
						   GetSQLValueString($_POST['sort'], "int"),
 						   GetSQLValueString($_POST['desc'], "text"),
						   GetSQLValueString($_POST['id'], "int"));
	
	  mysql_select_db($database_localhost, $localhost);
	  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());
	
	  $updateGoTo = "index.php";
	  if (isset($_SERVER['QUERY_STRING'])) {
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $_SERVER['QUERY_STRING'];
	  }
	  header(sprintf("Location: %s", $updateGoTo));
 	}
 } 


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <p class="phpshop123_title">更新品牌信息：<?php echo $row_brand['name']; ?></p>
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">名称:</td>
      <td><input name="name" type="text" value="<?php echo $row_brand['name']; ?>" size="32" maxlength="32">
      *</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">网址:</td>
      <td><input name="url" type="text" value="<?php echo $row_brand['url']; ?>" size="32" maxlength="60"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">排序:</td>
      <td><input name="sort" type="text" value="<?php echo $row_brand['sort']; ?>" size="32" maxlength="10"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">图片:</td>
      <td> 
	  	<?php if($row_brand['image_path']!=""){ ?>
	  <img src="<?php echo $row_brand['image_path']; ?>" />
         <?php } ?>
		<p>
          <input type="file" name="image_path" value="<?php echo $row_brand['image_path']; ?>" size="32">
      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">介绍:</td>
      <td><textarea name="desc" cols="50" rows="5"><?php echo $row_brand['desc']; ?></textarea>
      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="更新"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
  <input type="hidden" name="id" value="<?php echo $row_brand['id']; ?>">
</form>
<script language="JavaScript" type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script>
$().ready(function(){

	$("#form1").validate({
        rules: {
            name: {
                required: true
            },
			url:{
				url:true
			}, 
 			sort:{
				digits:true
			},
 			desc:{
				maxlength:100
			}
             
        },
        messages: {
            name: {
                required: "必填" 
            },
			url:{
				url:"网址格式不正确"
			}, 
 			sort:{
				digits:"只能是数字哦"
			},
 			desc:{
				minlength:"最多只能100个字符哦"
			}
        }
    });
	
});</script>
</body>
</html>
<?php
mysql_free_result($brand);
?>
