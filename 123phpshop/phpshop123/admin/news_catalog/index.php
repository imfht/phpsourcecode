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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
 
  $insertSQL = sprintf("INSERT INTO news_catalog (name) VALUES (%s)",
                       GetSQLValueString($_POST['name'], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
}

$maxRows_news_catalogs = 50;
$pageNum_news_catalogs = 0;
if (isset($_GET['pageNum_news_catalogs'])) {
  $pageNum_news_catalogs = $_GET['pageNum_news_catalogs'];
}
$startRow_news_catalogs = $pageNum_news_catalogs * $maxRows_news_catalogs;

mysql_select_db($database_localhost, $localhost);
$query_news_catalogs = "SELECT * FROM news_catalog where is_delete=0";
$query_limit_news_catalogs = sprintf("%s LIMIT %d, %d", $query_news_catalogs, $startRow_news_catalogs, $maxRows_news_catalogs);
$news_catalogs = mysql_query($query_limit_news_catalogs, $localhost) or die(mysql_error());
$row_news_catalogs = mysql_fetch_assoc($news_catalogs);

if (isset($_GET['totalRows_news_catalogs'])) {
  $totalRows_news_catalogs = $_GET['totalRows_news_catalogs'];
} else {
  $all_news_catalogs = mysql_query($query_news_catalogs);
  $totalRows_news_catalogs = mysql_num_rows($all_news_catalogs);
}
$totalPages_news_catalogs = ceil($totalRows_news_catalogs/$maxRows_news_catalogs)-1;

$queryString_news_catalogs = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_news_catalogs") == false && 
        stristr($param, "totalRows_news_catalogs") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_news_catalogs = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_news_catalogs = sprintf("&totalRows_news_catalogs=%d%s", $totalRows_news_catalogs, $queryString_news_catalogs);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form method="post" name="form1" id="form1" action="<?php echo $editFormAction; ?>">
  <p class="phpshop123_title">添加文章分类</p>
  <table align="center" class="phpshop123_search_box">
    <tr valign="baseline">
      <td nowrap align="right">分类名称:</td>
      <td><input type="text" class="required" name="name" value="" size="32">
      *
      <input name="submit" type="submit" value="插入记录" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1">
</form>
<?php if ($totalRows_news_catalogs > 0) { // Show if recordset not empty ?>
<table width="100%" border="1" class="phpshop123_list_box">
  <tr>
    <th scope="col">名称</th>
    <th scope="col">操作</th>
  </tr>
  <?php do { ?>
  <tr>
    <td><?php echo $row_news_catalogs['name']; ?></td>
    <td><div align="right"><a onclick="return confirm('您确认要删除这条记录吗？')" href="remove.php?id=<?php echo $row_news_catalogs['id']; ?>">删除</a> <a href="update.php?id=<?php echo $row_news_catalogs['id']; ?>">更新</a> <a href="../news/index.php?catalog_id=<?php echo $row_news_catalogs['id']; ?>">文章列表</a> <a href="../news/add.php?catalog_id=<?php echo $row_news_catalogs['id']; ?>">添加文章</a></div></td>
  </tr>
  <?php } while ($row_news_catalogs = mysql_fetch_assoc($news_catalogs)); ?>
</table>
<a href="<?php printf("%s?pageNum_news_catalogs=%d%s", $currentPage, 0, $queryString_news_catalogs); ?>"></a>
<?php } // Show if recordset not empty ?>
 
    <p>
      <script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
      <script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
      
      <script>
$().ready(function(){

	$("#form1").validate();
	
});</script>
</p>
    <p>
      <?php if ($pageNum_news_catalogs > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_news_catalogs=%d%s", $currentPage, 0, $queryString_news_catalogs); ?>" class="phpshop123_paging">第一页</a> <a href="<?php printf("%s?pageNum_news_catalogs=%d%s", $currentPage, max(0, $pageNum_news_catalogs - 1),$queryString_news_catalogs); ?>" class="phpshop123_paging">前一页</a>
      <?php } // Show if not first page ?> 
      <?php if ($pageNum_news_catalogs < $totalPages_news_catalogs) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_news_catalogs=%d%s", $currentPage, min($totalPages_news_catalogs, $pageNum_news_catalogs + 1), $queryString_news_catalogs); ?>" class="phpshop123_paging">下一页 </a>
       <a href="<?php printf("%s?pageNum_news_catalogs=%d%s", $currentPage, $totalPages_news_catalogs, $queryString_news_catalogs); ?>" class="phpshop123_paging">最后一页</a>
        <?php } // Show if not last page ?>
        </p>
</body>
</html>
<?php
mysql_free_result($news_catalogs);
?>
