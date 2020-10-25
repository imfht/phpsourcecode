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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

// 我们这里需要对上传文件进行检查
  include($_SERVER['DOCUMENT_ROOT'].'/Connections/lib/upload.php'); 
  
	$up = new fileupload;
    //设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
    $up -> set("path", $_SERVER['DOCUMENT_ROOT']."/uploads/brands/");
    $up -> set("maxsize", 2000000);
    $up -> set("allowtype", array("gif", "png", "jpg","jpeg"));
    $up -> set("israndname", true);
  
    //使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
    if($up->upload("image_path")) {
       	$image_path="/uploads/brands/".$up->getFileName(); 
	   
		 $insertSQL = sprintf("INSERT INTO brands (name, image_path, url, sort, `desc`) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString( $image_path, "text"),
                       GetSQLValueString($_POST['url'], "text"),
                       GetSQLValueString($_POST['sort'], "int"),
                       GetSQLValueString($_POST['desc'], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());

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
	
	
 
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">添加品牌</p>
<form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">名称:</td>
      <td><input type="text" name="name" value="" size="32" maxlength="32">
      *</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">图片:</td>
      <td><input type="file" name="image_path" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">网址:</td>
      <td><input name="url" type="text" value="http://" size="32" maxlength="60"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">排序:</td>
      <td><input name="sort" type="text" value="" size="32" maxlength="10"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">介绍:</td>
      <td><textarea name="desc" cols="50" rows="5"></textarea>
      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="添加"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1">
</form>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
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
				minlength:100
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
