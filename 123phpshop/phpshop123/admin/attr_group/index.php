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
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_attr = 50;
$pageNum_attr = 0;
if (isset($_GET['pageNum_attr'])) {
  $pageNum_attr = $_GET['pageNum_attr'];
}
$startRow_attr = $pageNum_attr * $maxRows_attr;

$colname_attr = "-1";
if (isset($_GET['product_type_id'])) {
  $colname_attr = (get_magic_quotes_gpc()) ? $_GET['product_type_id'] : addslashes($_GET['product_type_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_attr = sprintf("SELECT * FROM product_type_attr WHERE product_type_id = %s and is_delete=0", $colname_attr);
$query_limit_attr = sprintf("%s LIMIT %d, %d", $query_attr, $startRow_attr, $maxRows_attr);
$attr = mysql_query($query_limit_attr, $localhost) or die(mysql_error());
$row_attr = mysql_fetch_assoc($attr);

if (isset($_GET['totalRows_attr'])) {
  $totalRows_attr = $_GET['totalRows_attr'];
} else {
  $all_attr = mysql_query($query_attr);
  $totalRows_attr = mysql_num_rows($all_attr);
}
$totalPages_attr = ceil($totalRows_attr/$maxRows_attr)-1;

$queryString_attr = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_attr") == false && 
        stristr($param, "totalRows_attr") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_attr = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_attr = sprintf("&totalRows_attr=%d%s", $totalRows_attr, $queryString_attr);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%" border="0">
  <tr>
    <td class="phpshop123_title">产品分类属性列表</td>
    <td><div align="right"><a href="add.php?product_type_id=<?php echo $_GET['product_type_id']; ?>">添加属性</a></div></td>
  </tr>
</table>
<p align="left" class="phpshop123_infobox">没有记录，欢迎添加！</p>
<?php if ($totalRows_attr > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0" align="center" class="phpshop123_list_box">
    <tr>
      <td>ID</td>
      <td>名称</td>
      <td>是否可选</td>
      <td>输入方法</td>
      <td>可选的值</td>
      <td>操作</td>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_attr['id']; ?>&nbsp; </td>
        <td><a href="detail.php?recordID=<?php echo $row_attr['id']; ?>"> <?php echo $row_attr['name']; ?>&nbsp; </a> </td>
        <td><?php echo $product_type_selectable[$row_attr['is_selectable']]; ?>&nbsp; </td>
        <td><?php echo $product_type_input_method[$row_attr['input_method']]; ?>&nbsp; </td>
        <td><?php echo $row_attr['selectable_value']; ?>&nbsp; </td>
        <td><a href="remove.php?id=<?php echo $row_attr['id']; ?>" onclick="return confirm('您确定要删除这条记录吗？');">删除</a> <a href="update.php?id=<?php echo $row_attr['id']; ?>">更新</a> &nbsp; </td>
      </tr>
      <?php } while ($row_attr = mysql_fetch_assoc($attr)); ?>
      </table>
  <br>
  <table border="0" width="50%" align="right">
    <tr>
      <td width="23%" align="center"><?php if ($pageNum_attr > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_attr=%d%s", $currentPage, 0, $queryString_attr); ?>">第一页</a>
            <?php } // Show if not first page ?>      </td>
      <td width="31%" align="center"><?php if ($pageNum_attr > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_attr=%d%s", $currentPage, max(0, $pageNum_attr - 1), $queryString_attr); ?>">前一页</a>
            <?php } // Show if not first page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_attr < $totalPages_attr) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_attr=%d%s", $currentPage, min($totalPages_attr, $pageNum_attr + 1), $queryString_attr); ?>">下一页</a>
            <?php } // Show if not last page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_attr < $totalPages_attr) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_attr=%d%s", $currentPage, $totalPages_attr, $queryString_attr); ?>">最后一页</a>
            <?php } // Show if not last page ?>      </td>
    </tr>
      </table>
  记录 <?php echo ($startRow_attr + 1) ?> 到 <?php echo min($startRow_attr + $maxRows_attr, $totalRows_attr) ?> (总共 <?php echo $totalRows_attr ?>）
  <?php } // Show if recordset not empty ?></body>
</html>
<?php
mysql_free_result($attr);
?>
