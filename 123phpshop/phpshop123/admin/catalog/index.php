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
$currentPage = $_SERVER["PHP_SELF"];

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "new_catalog_form")) {
  $insertSQL = sprintf("INSERT INTO catalog (name, pid) VALUES (%s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['pid'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
}

$maxRows_catalogs = 50;
$pageNum_catalogs = 0;
if (isset($_GET['pageNum_catalogs'])) {
  $pageNum_catalogs = $_GET['pageNum_catalogs'];
}
$startRow_catalogs = $pageNum_catalogs * $maxRows_catalogs;

$colname_catalogs = "0";
if (isset($_GET['pid'])) {
  $colname_catalogs = (get_magic_quotes_gpc()) ? $_GET['pid'] : addslashes($_GET['pid']);
}
mysql_select_db($database_localhost, $localhost);
$query_catalogs = sprintf("SELECT * FROM `catalog` WHERE is_delete=0 and  pid = %s", $colname_catalogs);
$query_limit_catalogs = sprintf("%s LIMIT %d, %d", $query_catalogs, $startRow_catalogs, $maxRows_catalogs);
$catalogs = mysql_query($query_limit_catalogs, $localhost) or die(mysql_error());
$row_catalogs = mysql_fetch_assoc($catalogs);

if (isset($_GET['totalRows_catalogs'])) {
  $totalRows_catalogs = $_GET['totalRows_catalogs'];
} else {
  $all_catalogs = mysql_query($query_catalogs);
  $totalRows_catalogs = mysql_num_rows($all_catalogs);
}
$totalPages_catalogs = ceil($totalRows_catalogs/$maxRows_catalogs)-1;

$queryString_catalogs = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_catalogs") == false && 
        stristr($param, "totalRows_catalogs") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_catalogs = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_catalogs = sprintf("&totalRows_catalogs=%d%s", $totalRows_catalogs, $queryString_catalogs);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">产品分类 </p>
<form method="post" name="new_catalog_form" id="new_catalog_form"  action="<?php echo $editFormAction; ?>">
  <table align="center" class="phpshop123_search_box">
    <tr valign="baseline">
      <td nowrap align="right">分类名称:</td>
      <td><input name="name" type="text" class="required" id="name" value="" size="32" maxlength="20">
      <input name="submit" type="submit" value="添加分类" /></td>
    </tr>
  </table>
  <input type="hidden" name="pid" value="<?php echo isset($_GET['pid'])?$_GET['pid']:0; ?>">
  <input type="hidden" name="MM_insert" value="new_catalog_form">
</form>
<?php if ($totalRows_catalogs > 0) { // Show if recordset not empty ?>
  <table width="100%" border="1" cellpadding="0" cellspacing="0" class="phpshop123_list_box">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">名称</th>
      <th scope="col">操作</th>
    </tr>
    <?php do { ?>
      <tr>
        <td><div align="center"><?php echo $row_catalogs['id']; ?></div></td>
        <td><?php echo $row_catalogs['name']; ?></td>
        <td><div align="right"><a onClick="return confirm('您确认要删除这个分类吗？')" href="remove.php?id=<?php echo $row_catalogs['id']; ?>">删除</a> <a href="update.php?id=<?php echo $row_catalogs['id']; ?>">更新</a> <a href="index.php?pid=<?php echo $row_catalogs['id']; ?>">子分类列表</a> <a href="../product/index.php?catalog_id=<?php echo $row_catalogs['id']; ?>">产品列表</a> <a href="../product/add.php?catalog_id=<?php echo $row_catalogs['id']; ?>">添加产品</a></div></td>
      </tr>
        <?php } while ($row_catalogs = mysql_fetch_assoc($catalogs)); ?>
  </table>
  <br />
  <?php if ($pageNum_catalogs > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_catalogs=%d%s", $currentPage, 0, $queryString_catalogs); ?>" class="phpshop123_paging">第一页</a>
    <?php } // Show if not first page ?>
  <?php if ($pageNum_catalogs > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_catalogs=%d%s", $currentPage, max(0, $pageNum_catalogs - 1), $queryString_catalogs); ?>" class="phpshop123_paging">前一页</a>
    <?php } // Show if not first page ?>
  <?php if ($pageNum_catalogs < $totalPages_catalogs) { // Show if not last page ?>
    <a href="<?php printf("%s?pageNum_catalogs=%d%s", $currentPage, min($totalPages_catalogs, $pageNum_catalogs + 1), $queryString_catalogs); ?>" class="phpshop123_paging">下一页</a>
    <?php } // Show if not last page ?> 
   <?php if ($pageNum_catalogs < $totalPages_catalogs) { // Show if not last page ?>
     <a href="<?php printf("%s?pageNum_catalogs=%d%s", $currentPage, $totalPages_catalogs, $queryString_catalogs); ?>" class="phpshop123_paging">最后一页</a>
     <?php } // Show if not last page ?><br />
<?php } // Show if recordset not empty ?>
<?php if ($totalRows_catalogs == 0) { // Show if recordset empty ?>
  <p class="phpshop123_infobox">没有记录，欢迎添加。</p>
  <?php } // Show if recordset empty ?></body>
  
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script>
$().ready(function(){
	$("#new_catalog_form").validate({
        rules: {
            name: {
                required: true,
 				remote:{
                    url: "ajax_catalog_name.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'name': function(){return $("#name").val();}
                    }
					}
            } 
			
        },
        messages: {
			name: {
  				remote:"分类名称已存在"
            } 
        }
    });
	
});</script>

</html>
<?php
mysql_free_result($catalogs);
?>
