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

$maxRows_admins = 10;
$pageNum_admins = 0;
if (isset($_GET['pageNum_admins'])) {
  $pageNum_admins = $_GET['pageNum_admins'];
}
$startRow_admins = $pageNum_admins * $maxRows_admins;

mysql_select_db($database_localhost, $localhost);
$query_admins = "SELECT * FROM member where is_delete=0";
$query_limit_admins = sprintf("%s LIMIT %d, %d", $query_admins, $startRow_admins, $maxRows_admins);
$admins = mysql_query($query_limit_admins, $localhost) or die(mysql_error());
$row_admins = mysql_fetch_assoc($admins);

if (isset($_GET['totalRows_admins'])) {
  $totalRows_admins = $_GET['totalRows_admins'];
} else {
  $all_admins = mysql_query($query_admins);
  $totalRows_admins = mysql_num_rows($all_admins);
}
$totalPages_admins = ceil($totalRows_admins/$maxRows_admins)-1;

$queryString_admins = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_admins") == false && 
        stristr($param, "totalRows_admins") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_admins = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_admins = sprintf("&totalRows_admins=%d%s", $totalRows_admins, $queryString_admins);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">管理员列表</p>
<div align="right">
  <?php if ($totalRows_admins > 0) { // Show if recordset not empty ?>
    
    <table width="100%" border="1" align="center" class="phpshop123_list_box">
      <tr>
        <th>账户</th>
        <th>手机</th>
        <th>邮箱</th>
        <th>注册时间</th>
        <th>操作</th>
      </tr>
      <?php do { ?>
        <tr>
          <td><a href="detail.php?recordID=<?php echo $row_admins['id']; ?>"> <?php echo $row_admins['username']; ?>&nbsp; </a> </td>
          <td><?php echo $row_admins['mobile']; ?>&nbsp; </td>
          <td><?php echo $row_admins['email']; ?>&nbsp; </td>
          <td><?php echo $row_admins['register_at']; ?>&nbsp; </td>
          <td><div align="right"><a onclick="return confirm('您确实要删除这条记录吗？')" href="remove.php?id=<?php echo $row_admins['id']; ?>">删除</a> <a href="update.php?id=<?php echo $row_admins['id']; ?>">更新</a></div></td>
        </tr>
        <?php } while ($row_admins = mysql_fetch_assoc($admins)); ?>
    </table>
    <div align="right"> 
		<p>
		 <?php if ($pageNum_admins > 0) { // Show if not first page ?>
         <a href="<?php printf("%s?pageNum_admins=%d%s", $currentPage, 0, $queryString_admins); ?>" class="phpshop123_paging">第一页</a>
         <?php } // Show if not first page ?> 
		 
       <?php if ($pageNum_admins > 0) { // Show if not first page ?>
         <a href="<?php printf("%s?pageNum_admins=%d%s", $currentPage, max(0, $pageNum_admins - 1), $queryString_admins); ?>" class="phpshop123_search_box">前一页</a>
         <?php } // Show if not first page ?>
      
	     <?php if ($pageNum_admins < $totalPages_admins) { // Show if not last page ?>
         <a href="<?php printf("%s?pageNum_admins=%d%s", $currentPage, min($totalPages_admins, $pageNum_admins + 1), $queryString_admins); ?>" class="phpshop123_paging">下一页</a>
	       <?php } // Show if not last page ?> 
	  	<?php if ($pageNum_admins < $totalPages_admins) { // Show if not last page ?>
	    <a href="<?php printf("%s?pageNum_admins=%d%s", $currentPage, $totalPages_admins, $queryString_admins); ?>" class="phpshop123_paging">最后一页</a>
	    <?php } // Show if not last page ?>    
	</div>
    <?php } // Show if recordset not empty ?>
  
  <?php if ($totalRows_admins == 0) { // Show if recordset empty ?>
</div>
  <div class="phpshop123_infobox"><p align="right">暂无记录！<a href="add.php">欢迎添加</a></p>
</div>
  <div align="right">
    <?php } // Show if recordset empty ?>
  
</div>
</body>
</html>
<?php
mysql_free_result($admins);
?>
