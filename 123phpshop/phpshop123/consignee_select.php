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
<?php require_once('Connections/localhost.php'); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Connections/lib/product.php");?>
<?php
$result=array('code'=>'0','message'=>'SUCCEED','data'=>array());
$colname_consignee = "-1";
if (isset($_POST['consignee_id'])) {
  $colname_consignee = (get_magic_quotes_gpc()) ? $_POST['consignee_id'] : addslashes($_POST['consignee_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_consignee = sprintf("SELECT * FROM user_consignee WHERE id = %s and user_id= %s", $colname_consignee,$_SESSION['user_id']);
$consignee = mysql_query($query_consignee, $localhost) or die(mysql_error());
$row_consignee = mysql_fetch_assoc($consignee);
$totalRows_consignee = mysql_num_rows($consignee);
if($totalRows_consignee==0){
		$result=array('code'=>'1','message'=>'id错误，请刷新页面重试！');
}else{

	$_SESSION['consignee']['id']=$_POST['consignee_id'];
	$_SESSION['consignee']['name']=$_POST['consignee_name'];
	$_SESSION['consignee']['mobile']=$_POST['consignee_mobile'];
	$_SESSION['consignee']['province']=$_POST['consignee_province'];
	$_SESSION['consignee']['city']=$_POST['consignee_city'];
	$_SESSION['consignee']['district']=$_POST['consignee_district'];
	$_SESSION['consignee']['address']=$_POST['consignee_address'];
	$_SESSION['consignee']['zip']=$_POST['consignee_zip'];
	$result['data']=$_SESSION['consignee'];
	$areas[]=trim($_POST['consignee_province'])."_*_*";
	$areas[]=trim($_POST['consignee_province'])."_".trim($_POST['consignee_city'])."_*";
	$areas[]=trim($_POST['consignee_province'])."_".trim($_POST['consignee_city'])."_".trim($_POST['consignee_district']);
	$result['could_deliver']=could_devliver($areas)?"1":"0";
}

?>
<?php
die(json_encode($result));
?>