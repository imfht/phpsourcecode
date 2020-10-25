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

$maxRows_ads = 50;
$pageNum_ads = 0;
if (isset($_GET['pageNum_ads'])) {
  $pageNum_ads = $_GET['pageNum_ads'];
}
$startRow_ads = $pageNum_ads * $maxRows_ads;

mysql_select_db($database_localhost, $localhost);
$query_ads = "SELECT * FROM ad";
$query_limit_ads = sprintf("%s LIMIT %d, %d", $query_ads, $startRow_ads, $maxRows_ads);
$ads = mysql_query($query_limit_ads, $localhost) or die(mysql_error());
$row_ads = mysql_fetch_assoc($ads);

if (isset($_GET['totalRows_ads'])) {
  $totalRows_ads = $_GET['totalRows_ads'];
} else {
  $all_ads = mysql_query($query_ads);
  $totalRows_ads = mysql_num_rows($all_ads);
}
$totalPages_ads = ceil($totalRows_ads/$maxRows_ads)-1;

$queryString_ads = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ads") == false && 
        stristr($param, "totalRows_ads") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ads = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ads = sprintf("&totalRows_ads=%d%s", $totalRows_ads, $queryString_ads);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">广告列表</p>
<?php if ($totalRows_ads == 0) { // Show if recordset empty ?>
  <p class="phpshop123_infobox"><a href="add.php">现在还没有，赶紧添加一个吧。</a></p>
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_ads > 0) { // Show if recordset not empty ?>
  <p>
  <table width="100%" border="1" align="center" class="phpshop123_list_box">
    <tr>
      <th>ID</th>
      <th>名称</th>
      <th>介绍</th>
      <th>创建时间</th>
      <th>操作</th>
    </tr>
    <?php do { ?>
      <tr>
        <td><div align="center"><?php echo $row_ads['id']; ?>&nbsp; </div></td>
        <td><a href="detail.php?recordID=<?php echo $row_ads['id']; ?>"> <?php echo $row_ads['name']; ?>&nbsp; </a> </td>
        <td><?php echo $row_ads['intro']; ?>&nbsp; </td>
        <td><?php echo $row_ads['create_time']; ?></td>
        <td><div align="right"><a onClick="return confirm('你确实要删除这条广告吗？')" href="remove.php?id=<?php echo $row_ads['id']; ?>">删除</a> <a href="update.php?id=<?php echo $row_ads['id']; ?>">更新</a> <a href="add_images.php?id=<?php echo $row_ads['id']; ?>"></a></div></td>
      </tr>
      <?php } while ($row_ads = mysql_fetch_assoc($ads)); ?>
  </table>
  <br>
  <table border="0" width="50%" align="right">
    <tr>
      <td width="23%" align="center"><?php if ($pageNum_ads > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_ads=%d%s", $currentPage, 0, $queryString_ads); ?>" class="phpshop123_paging">第一页</a>
            <?php } // Show if not first page ?>      </td>
      <td width="31%" align="center"><?php if ($pageNum_ads > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_ads=%d%s", $currentPage, max(0, $pageNum_ads - 1), $queryString_ads); ?>" class="phpshop123_paging">前一页</a>
            <?php } // Show if not first page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_ads < $totalPages_ads) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_ads=%d%s", $currentPage, min($totalPages_ads, $pageNum_ads + 1), $queryString_ads); ?>" class="phpshop123_paging">下一页</a>
            <?php } // Show if not last page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_ads < $totalPages_ads) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_ads=%d%s", $currentPage, $totalPages_ads, $queryString_ads); ?>" class="phpshop123_paging">最后一页</a>
            <?php } // Show if not last page ?>      </td>
    </tr>
  </table>
  记录 <?php echo ($startRow_ads + 1) ?> 到 <?php echo min($startRow_ads + $maxRows_ads, $totalRows_ads) ?> (总共 <?php echo $totalRows_ads ?>）
  </p>
   <?php } // Show if recordset not empty ?>
</body>
</html>
<?php
mysql_free_result($ads);
?>
