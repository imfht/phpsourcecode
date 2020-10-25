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
$where_query_string=_get_comment_where_query_string($_GET);
$maxRows_comments = 50;
$pageNum_comments = 0;
if (isset($_GET['pageNum_comments'])) {
  $pageNum_comments = $_GET['pageNum_comments'];
}
$startRow_comments = $pageNum_comments * $maxRows_comments;

mysql_select_db($database_localhost, $localhost);
$query_comments = "SELECT * FROM product_comment where is_delete=0 $where_query_string ORDER BY id DESC";
$query_limit_comments = sprintf("%s LIMIT %d, %d", $query_comments, $startRow_comments, $maxRows_comments);
$comments = mysql_query($query_limit_comments, $localhost) or die(mysql_error());
$row_comments = mysql_fetch_assoc($comments);

if (isset($_GET['totalRows_comments'])) {
  $totalRows_comments = $_GET['totalRows_comments'];
} else {
  $all_comments = mysql_query($query_comments);
  $totalRows_comments = mysql_num_rows($all_comments);
}
$totalPages_comments = ceil($totalRows_comments/$maxRows_comments)-1;

$queryString_comments = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_comments") == false && 
        stristr($param, "totalRows_comments") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_comments = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_comments = sprintf("&totalRows_comments=%d%s", $totalRows_comments, $queryString_comments);

function _get_comment_where_query_string($get){
 
  	$where_string='';
	
	if(isset($get['message']) && trim($get['message'])!=''){
  		 
		$where_string.=" and  message like'%".$get['message']."%'";
	}
	
	if( isset($get['create_from']) && trim($get['create_from'])!='' && isset($get['create_end']) && trim($get['create_end'])!=''){
  		$where_string.=" and  create_time between '".$get['create_from']. "' and '" .$get['create_end'] ." 23:59:59'";
	}
	
	return $where_string;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>

  <p class="phpshop123_title">评论搜索</p>
  <form id="comment_search" name="comment_search" method="get" action="">
    <table width="100%" border="0" class="phpshop123_search_box">
      <tr>
        <td>评论内容</td>
        <td><input name="message" type="text" id="message"  value="<?php echo isset($_GET['message'])?$_GET['message']:''; ?>"/></td>
        <td>发表时间</td>
        <td><input name="create_from" type="text" id="create_from" value="<?php echo isset($_GET['create_from'])?$_GET['create_from']:''; ?>"/>
          <input name="create_end" type="text" id="create_end" value="<?php echo isset($_GET['create_end'])?$_GET['create_end']:''; ?>"/></td>
        <td><label>
          <div align="right">
            <input type="submit" name="Submit" value="搜索" />
            </div>
        </label></td>
      </tr>
    </table>
  </form>
  <p class="phpshop123_title">评论列表</p>
  <?php if ($totalRows_comments > 0) { // Show if recordset not empty ?>
  <table width="100%" border="1" align="center" class="phpshop123_list_box">
    <tr>
      <th>ID</th>
      <th>用户</th>
      <th>消息</th>
      <th>提交时间</th>
      <th>操作</th>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_comments['id']; ?>&nbsp; </td>
        <td><?php echo $row_comments['user_id']; ?>&nbsp; </td>
        <td><a href="detail.php?recordID=<?php echo $row_comments['id']; ?>"> <?php echo $row_comments['message']; ?>&nbsp; </a> </td>
        <td><?php echo $row_comments['create_time']; ?>&nbsp; </td>
        <td><div align="right"><a onClick="return confirm('你确认要删除这条记录吗？');" href="remove.php?id=<?php echo $row_comments['id']; ?>">删除</a> </div></td>
      </tr>
      <?php } while ($row_comments = mysql_fetch_assoc($comments)); ?>
  </table>
  <br>
  <table border="0" width="50%" align="right">
    <tr>
      <td width="23%" align="center"><?php if ($pageNum_comments > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, 0, $queryString_comments); ?>" class="phpshop123_paging">第一页</a>
            <?php } // Show if not first page ?>      </td>
      <td width="31%" align="center"><?php if ($pageNum_comments > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, max(0, $pageNum_comments - 1), $queryString_comments); ?>" class="phpshop123_paging">前一页</a>
            <?php } // Show if not first page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_comments < $totalPages_comments) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, min($totalPages_comments, $pageNum_comments + 1), $queryString_comments); ?>" class="phpshop123_paging">下一页</a>
            <?php } // Show if not last page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_comments < $totalPages_comments) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, $totalPages_comments, $queryString_comments); ?>" class="phpshop123_paging">最后一页</a>
            <?php } // Show if not last page ?>      </td>
    </tr>
  </table>
  记录 <?php echo ($startRow_comments + 1) ?> 到 <?php echo min($startRow_comments + $maxRows_comments, $totalRows_comments) ?> (总共 <?php echo $totalRows_comments ?>) 
  </p>
   
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_comments == 0) { // Show if recordset empty ?>
    <p class="phpshop123_infobox">暂无评论！</p>
    <?php } // Show if recordset empty ?>
	<link rel="stylesheet" href="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.css">
	<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
	<script>
	 $(function() {
 		$("#create_from" ).datepicker({dateFormat: 'yy-mm-dd' });
		$("#create_end" ).datepicker({dateFormat: 'yy-mm-dd' });
    });</script>
	</body>
</html>
<?php
mysql_free_result($comments);
?>
