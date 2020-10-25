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
$maxRows_orders = 10;
$pageNum_orders = 0;
if (isset($_GET['pageNum_orders'])) {
  $pageNum_orders = $_GET['pageNum_orders'];
}
$startRow_orders = $pageNum_orders * $maxRows_orders;

$colname_orders = "-1";
if (isset($_SESSION['user_id'])) {
  $colname_orders = (get_magic_quotes_gpc()) ? $_SESSION['user_id'] : addslashes($_SESSION['user_id']);
}
mysql_select_db($database_localhost, $localhost);
$where=_get_order_where($_GET);

$query_orders = "SELECT * from orders $where order by id desc";
$query_limit_orders = sprintf("%s LIMIT %d, %d ", $query_orders, $startRow_orders, $maxRows_orders);
$orders = mysql_query($query_limit_orders, $localhost) or die(mysql_error());
$row_orders = mysql_fetch_assoc($orders);

if (isset($_GET['totalRows_orders'])) {
  $totalRows_orders = $_GET['totalRows_orders'];
} else {
  $all_orders = mysql_query($query_orders);
  $totalRows_orders = mysql_num_rows($all_orders);
}
$totalPages_orders = ceil($totalRows_orders/$maxRows_orders)-1;


$queryString_order = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_orders") == false && 
        stristr($param, "totalRows_order") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_order = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_order = sprintf("&totalRows_order=%d%s", $totalRows_orders, $queryString_order);

?>

<?php 
function _get_order_where($get){
	
 	$where_string=' where is_delete=0 and user_id='.$_SESSION['user_id'];
	
	if(isset($get['sn']) && trim($get['sn'])!=''){
 		$where_string.=" and sn='".$get['sn']."'";
	}
	
	if(isset($get['order_status']) && trim($get['order_status'])!=''){
 		$where_string.=" and ";
		$where_string.=" order_status='".$get['order_status']."'";
	}
	
	if( isset($get['delivery_from']) && trim($get['delivery_from'])!='' && isset($get['delivery_end']) && trim($get['delivery_end'])!=''){
 		$where_string.=" and ";
		$where_string.=" delivery_at between '".$get['delivery_from']. "  00:00:00' and '" .$get['delivery_end'] ."  23:59:59'";
	}
	
	if(isset($get['pay_from']) && trim($get['pay_from'])!='' && isset($get['pay_end']) && trim($get['pay_end'])!=''){
 		$where_string.=" and ";
		$where_string.=" pay_at between '".$get['pay_from']. "  00:00:00' and '" .$get['pay_end']."  23:59:59'" ;
	
	}
	
	if(isset($get['create_from']) && trim($get['create_from'])!='' && isset($get['create_from']) && trim($get['create_end'])!=''){
		
 		$where_string.=" and ";
		$where_string.=" create_time between '".$get['create_from']. "  00:00:00' and '" .$get['create_end']." 23:59:59'" ;
	}

	return $where_string;
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
<!--
body {
	background-color: #f5f5f5;
}
.STYLE1 {
	font-size: 18px;
	font-weight: bold;
}
body,td,th {
	font-size: 12px;
}
.STYLE2 {color: #AAA}
a{
	text-decoration:none;
	color:#000000;
}
-->
</style>
<link href="../../css/common_user.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="phpshop123_user_title">我的订单</div>
<form id="order_search" name="order_search" method="get">
  <br />
  <table width="100%" border="0" bgcolor="#FFFFFF">
    <tr>
      <td><table width="100%" border="0">
        <tr>
          <td>订单序列号</td>
          <td><input name="sn" class="digits" type="text" id="sn" value="<?php echo isset($_GET['sn'])?$_GET['sn']:''; ?>" maxlength="18" />          </td>
          <td>订单状态</td>
          <td><label>
            <select name="order_status" id="order_status">
              <option value="" <?php if(isset($_GET['order_status']) && $_GET['order_status']==''){ ?>selected<?php } ?>>不限制</option>
              <?php foreach($order_status as $key=>$value){ ?>
              <option value="<?php echo $key;?>" <?php if(isset($_GET['order_status']) && $_GET['order_status']==$key && $_GET['order_status']!=''){ ?>selected<?php } ?>><?php echo $value;?></option>
              <?php } ?>
            </select>
          </label></td>
        </tr>
        <tr>
          <td>创建时间</td>
          <td><input name="create_from" type="text" id="create_from" value="<?php echo isset($_GET['create_from'])?$_GET['create_from']:''; ?>" />
              <input name="create_end" type="text" id="create_end" value="<?php echo isset($_GET['create_end'])?$_GET['create_end']:''; ?>" /></td>
          <td>发货时间</td>
          <td><input name="delivery_from" type="text" id="delivery_from" value="<?php echo isset($_GET['delivery_from'])?$_GET['delivery_from']:''; ?>" />
              <input name="delivery_end" type="text" id="delivery_end" value="<?php echo isset($_GET['delivery_end'])?$_GET['delivery_end']:''; ?>" /></td>
        </tr>
        <tr>
          <td>付款时间</td>
          <td><input name="pay_from" type="text" id="pay_from" value="<?php echo isset($_GET['pay_from'])?$_GET['pay_from']:''; ?>" />
              <input name="pay_end" type="text" id="pay_end" value="<?php echo isset($_GET['pay_end'])?$_GET['pay_end']:''; ?>" /></td>
          <td colspan="2"> 
              <div align="right">
                <input type="submit" name="Submit" value="搜索" />
              </div>            </td>
        </tr>
      </table></td>
    </tr>
  </table>
  <p align="right">&nbsp;</p>
</form>

<?php if ($totalRows_orders > 0) { // Show if recordset not empty ?>
<div style="background-color:white;"> <br />
    <br />
          <table width="100%" height="34" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td bgcolor="#f5f5f5"><table width="100%" border="0">
                <tr>
                  <td width="434" height="31"><div align="center">订单详情</div></td>
                  <td width="486"><div align="center">收货人</div></td>
                  <td width="118"><div align="center">总计</div></td>
                  <td width="243"><div align="center">全部状态</div></td>
                  <td width="360"><div align="center">操作</div></td>
                </tr>
              </table></td>
            </tr>
          </table>
        <br />
		 <?php do { ?>
            <br />
              <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#f5f5f5">
       <tr bgcolor="#f5f5f5">
        <td height="31" colspan="5" bgcolor="#f5f5f5"><span class="STYLE2"><?php echo $row_orders['create_time']; ?>&nbsp;&nbsp;&nbsp;订单号：</span><a href="detail.php?sn=<?php echo $row_orders['sn']; ?>"><?php echo $row_orders['sn']; ?></a></td>
      </tr>
      <tr>
        <td width="433" height="91">
		<?php 
		
 		mysql_select_db($database_localhost, $localhost);
		$query_order_items = "SELECT * FROM order_item WHERE order_id =". $row_orders['id'];
		$order_items = mysql_query($query_order_items, $localhost) or die(mysql_error());
		//$row_order_items = mysql_fetch_assoc($order_items);
		//var_dump($row_order_items);
		$totalRows_order_items = mysql_num_rows($order_items);
		
		if($totalRows_order_items>0){
		
			while($order_item_row=mysql_fetch_object($order_items)){
 				mysql_select_db($database_localhost, $localhost);
				$query_order_images = "SELECT * FROM product_images WHERE product_id = ".$order_item_row->product_id." limit 1";
				$order_images = mysql_query($query_order_images, $localhost) or die(mysql_error());
				//	$row_order_images = mysql_fetch_assoc($order_images);
				$totalRows_order_images = mysql_num_rows($order_images);
				
				if($totalRows_order_images>0){ 
					while($row_order_images_row=mysql_fetch_object($order_images)){?>
						<a href="/product.php?id=<?php echo $order_item_row->product_id;?>" target="_parent"><img src="<?php echo $row_order_images_row->image_files==null?"/uploads/default_product.png":$row_order_images_row->image_files;?>" width="50" height="50" border="1" style="border:1px solid #f5f5f5;margin-left:14px;"/>	</a>
					<?php 
					}
				 }else{?>
				 
				 <a href="/product.php?id=<?php echo $order_item_row->product_id;?>" target="_parent"><img src="/uploads/default_product.png" width="50" height="50" border="1" style="border:1px solid #f5f5f5;margin-left:14px;"/>	</a>
 				 <?php }
		
			}	
		}
		
		
		?>		</td>
        <td width="484"><div align="center"><?php echo $row_orders['consignee_name']=='NULL'?"未知":$row_orders['consignee_name']; ?></div></td>
        <td width="116">￥<?php echo $row_orders['should_paid'];?></td>
        <td width="242"><div align="center"><?php echo isset($order_status[$row_orders['order_status']])?$order_status[$row_orders['order_status']]:"未知"; ?></div></td>
        <td width="354"><div align="center"><a href="detail.php?sn=<?php echo $row_orders['sn']; ?>">[订单详细]</a> 
              <?php if(should_pay($row_orders['order_status'])){?>
          <a href="../../payoff.php?order_sn=<?php echo $row_orders['sn']; ?>" target="_parent">[支付]</a>
              <?php }?>
              <?php if(could_withdraw($row_orders['order_status'])){?>
          [<a href="withdraw.php?id=<?php echo $row_orders['id']; ?>"  onClick="return confirm('您确实要撤销订单吗?');">[撤销订单]</a>
              <?php }?>
          
              <?php if(could_recieved($row_orders['order_status'])){?>
          <a href="_received.php?id=<?php echo $row_orders['id']; ?>" onClick="return confirm('请确认已经收到货物了');">[确认收货]</a>
              <?php }?>  
			  
			  <?php if(could_return($row_orders['order_status'])){?>
          <a href="_apply_return.php?id=<?php echo $row_orders['id']; ?>" onClick="return confirm('您确实要申请退货吗?');">[申请退货]</a>
              <?php }?>  
			         
        </div></td>
      </tr>
  </table>
   <?php } while ($row_orders = mysql_fetch_assoc($orders)); ?>
  <br />
</div>
  <br />
  <table width="100%" border="0">
    <tr>
      <td height="31">
	  <div align="right">
	  <?php if ($pageNum_orders > 0) { // Show if not first page ?>
	  <a href="<?php printf("%s?pageNum_orders=%d%s", $currentPage, max(0, $pageNum_orders - 1), $queryString_order); ?>" class="phpshop123_user_paging">第一页</a>
	  <?php  } ?>
	  
	  <?php if ($pageNum_orders > 0) { // Show if not first page ?>
	  
	  
<a href="<?php printf("%s?pageNum_orders=%d%s", $currentPage, max(0, $pageNum_orders - 1), $queryString_order); ?>" class="phpshop123_user_paging">前一页</a>
 <?php  } ?>
 
 <?php if ($pageNum_orders < $totalPages_orders) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_orders=%d%s", $currentPage, min($totalPages_orders, $pageNum_orders + 1), $queryString_order); ?>" class="phpshop123_user_paging">下一页</a> 
  <?php  } ?>
<?php if ($pageNum_orders < $totalPages_orders) { // Show if not last page ?>

<a href="<?php printf("%s?pageNum_orders=%d%s", $currentPage, $totalPages_orders, $queryString_order); ?>" class="phpshop123_user_paging">最后一页</a>  <?php  } ?></div></td>
    </tr>
  </table>
  <?php } // Show if recordset not empty ?>
   
<?php if ($totalRows_orders == 0) { // Show if recordset empty ?>
  <p class="phpshop123_user_title"><a href="../../index.php" target="_parent" >没有记录，赶紧购物吧。</a> </p>
  <?php } // Show if recordset empty ?>  
 <link rel="stylesheet" href="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.css">
<script  src="../../js/jquery-1.7.2.min.js"></script>
<script   src="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script   src="../../js/jquery.validate.min.js"></script>
<script>
 $(function() {
	$("#order_search").validate();
	$( "#pay_from" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#pay_end" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#delivery_from" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#delivery_end" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#create_from" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#create_end" ).datepicker({ dateFormat: 'yy-mm-dd' });
});
</script>
</body>
</html>
<?php

isset($order_items)?mysql_free_result($order_items):'';

isset($order_images)?mysql_free_result($order_images):'';

isset($orders)?mysql_free_result($orders):'';
?>
