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
$result="true";
$colname_news = "-1";
if (isset($_POST['title'])) {
  $colname_news = (get_magic_quotes_gpc()) ? $_POST['title'] : addslashes($_POST['title']);
}
mysql_select_db($database_localhost, $localhost);
$query_news = sprintf("SELECT * FROM news WHERE title = '%s'", $colname_news);
$news = mysql_query($query_news, $localhost) or die(mysql_error());
$row_news = mysql_fetch_assoc($news);
$totalRows_news = mysql_num_rows($news);
if($totalRows_news>0){
	$result="false";
}
?> 
<?php
mysql_free_result($news);
die($result);
?>