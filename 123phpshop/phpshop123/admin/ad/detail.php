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
    $up -> set("path", $_SERVER['DOCUMENT_ROOT']."/uploads/ad/");
    $up -> set("maxsize", 2000000);
    $up -> set("allowtype", array("gif", "png", "jpg","jpeg"));
    $up -> set("israndname", true);
  
    //使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
    if($up->upload("image_path")) {
       $image_path="/uploads/ad/".$up->getFileName(); 
	   $insertSQL = sprintf("INSERT INTO ad_images (ad_id, image_path, link_url) VALUES (%s, %s, %s)",
						   GetSQLValueString($_POST['ad_id'], "int"),
						   GetSQLValueString($image_path, "text"),
						   GetSQLValueString($_POST['link_url'], "text"));
	
	  mysql_select_db($database_localhost, $localhost);
	  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
	  
    } else {
        echo '<pre>';
        //获取上传失败以后的错误提示
        var_dump($up->getErrorMsg());
        echo '</pre>';
    }
}

$colname_ad_images = "-1";
if (isset($_GET['recordID'])) {
  $colname_ad_images = (get_magic_quotes_gpc()) ? $_GET['recordID'] : addslashes($_GET['recordID']);
}
mysql_select_db($database_localhost, $localhost);
$query_ad_images = sprintf("SELECT * FROM ad_images WHERE ad_id = %s", $colname_ad_images);
$ad_images = mysql_query($query_ad_images, $localhost) or die(mysql_error());
$row_ad_images = mysql_fetch_assoc($ad_images);
$totalRows_ad_images = mysql_num_rows($ad_images);

$maxRows_DetailRS1 = 50;
$pageNum_DetailRS1 = 0;
if (isset($_GET['pageNum_DetailRS1'])) {
  $pageNum_DetailRS1 = $_GET['pageNum_DetailRS1'];
}
$startRow_DetailRS1 = $pageNum_DetailRS1 * $maxRows_DetailRS1;

mysql_select_db($database_localhost, $localhost);
$recordID = $_GET['recordID'];
$query_DetailRS1 = "SELECT * FROM ad WHERE id = $recordID";
$query_limit_DetailRS1 = sprintf("%s LIMIT %d, %d", $query_DetailRS1, $startRow_DetailRS1, $maxRows_DetailRS1);
$DetailRS1 = mysql_query($query_limit_DetailRS1, $localhost) or die(mysql_error());
$row_DetailRS1 = mysql_fetch_assoc($DetailRS1);

if (isset($_GET['totalRows_DetailRS1'])) {
  $totalRows_DetailRS1 = $_GET['totalRows_DetailRS1'];
} else {
  $all_DetailRS1 = mysql_query($query_DetailRS1);
  $totalRows_DetailRS1 = mysql_num_rows($all_DetailRS1);
}
$totalPages_DetailRS1 = ceil($totalRows_DetailRS1/$maxRows_DetailRS1)-1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">广告详细</p>
<table width="100%" border="0" align="center" class="phpshop123_form_box">
  <tr>
    <td>广告名称</td>
    <td><?php echo $row_DetailRS1['name']; ?> </td>
  </tr>
  <tr>
    <td>备忘</td>
    <td><?php echo $row_DetailRS1['intro']; ?> </td>
  </tr>
  <tr>
    <td>高度</td>
    <td><?php echo $row_DetailRS1['image_height']; ?></td>
  </tr>
  <tr>
    <td>宽度</td>
    <td><?php echo $row_DetailRS1['image_width']; ?></td>
  </tr>
  <tr>
    <td>创建时间</td>
    <td><?php echo $row_DetailRS1['create_time']; ?> </td>
  </tr>
</table>
<br />
<span class="phpshop123_title">添加图片</span>
<form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1"  id="form1" style="border:1px solid #e8e8e8;margin-top:10px;padding:10px;">
     <table width="100%" align="center">
      <tr valign="baseline">
        <td nowrap align="right">上传图片:</td>
        <td><input name="image_path"   type="file" id="image_path" value="" size="32">
        *</td>
      </tr>
      <tr valign="baseline">
        <td nowrap align="right">链接网址:</td>
        <td><input name="link_url" type="text"  id="link_url" value="http://www." size="32" maxlength="100">
        *</td>
      </tr>
      <tr valign="baseline">
        <td nowrap align="right">&nbsp;</td>
        <td><input type="submit" value="添加图片"></td>
      </tr>
  </table>
    <input type="hidden" name="ad_id" value="<?php echo $row_DetailRS1['id']; ?> ">
    <input type="hidden" name="MM_insert" value="form1">
</form>
   
 <?php if ($totalRows_ad_images > 0) { // Show if recordset not empty ?>
  <p class="phpshop123_title">图片列表</p>
  <table width="100%" border="0" class="phpshop123_list_box">
    <tr>
      <th scope="row">图片</th>
      <td>链接地址</td>
      <td>创建时间</td>
      <td>操作</td>
    </tr>
    <?php do { ?>
      <tr>
        <th scope="row"><img src="<?php echo $row_ad_images['image_path']; ?>" width="20" height="20" /></th>
        <td><?php echo $row_ad_images['link_url']; ?></td>
        <td><?php echo $row_ad_images['create_time']; ?></td>
        <td><a onClick="return confirm('你确定要删除这条广告吗？');" href="../add_images/remove.php?id=<?php echo $row_ad_images['id']; ?>">删除</a> <a href="../add_images/update.php?id=<?php echo $row_ad_images['id']; ?>"></a></td>
      </tr>
      <?php } while ($row_ad_images = mysql_fetch_assoc($ad_images)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
  
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script>
$().ready(function(){

	$("#form1").validate({
        rules: {
		
            image_path: {
                required: true 
            },
			
            link_url: {
                required: true,
				url:true
            } 
        },
        messages: {
		
            image_path: {
                required: "必填" 
            },
			
            link_url: {
                required: "必填" ,
				url:"请输入合法网址"
            } 
        }
    });
	
});</script>
</body>
</html><?php
mysql_free_result($ad_images);

mysql_free_result($DetailRS1);
?>
