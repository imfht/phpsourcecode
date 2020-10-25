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

$maxRows_orders = 50;
$pageNum_orders = 0;
if (isset($_GET['pageNum_orders'])) {
  $pageNum_orders = $_GET['pageNum_orders'];
}
$startRow_orders = $pageNum_orders * $maxRows_orders;
$where=_get_order_where($_GET);
mysql_select_db($database_localhost, $localhost);
$query_orders = "SELECT orders.*,user.username FROM `orders` inner join user on user.id=orders.user_id where orders.is_delete=0 $where order by orders.id desc";
$query_limit_orders = sprintf("%s LIMIT %d, %d", $query_orders, $startRow_orders, $maxRows_orders);
$orders = mysql_query($query_limit_orders, $localhost) or die(mysql_error());
$row_orders = mysql_fetch_assoc($orders);

if (isset($_GET['totalRows_orders'])) {
  $totalRows_orders = $_GET['totalRows_orders'];
} else {
  $all_orders = mysql_query($query_orders);
  $totalRows_orders = mysql_num_rows($all_orders);
}
$totalPages_orders = ceil($totalRows_orders/$maxRows_orders)-1;

$queryString_orders = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_orders") == false && 
        stristr($param, "totalRows_orders") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_orders = "&" . htmlentities(implode("&", $newParams));
  }
}

$queryString_orders = sprintf("&totalRows_orders=%d%s", $totalRows_orders, $queryString_orders);


function _get_order_where($get){
	
	 
	 
	$where_string='';
	
	if(isset($get['sn']) && trim($get['sn'])!=''){
  		$where_string.=" and  sn='".$get['sn']."'";
	}
	
	if(isset($get['status']) && trim($get['status'])!=''){
  		
		$where_string.=" and order_status='".$get['status']."'";
	}
	
	if( isset($get['delivery_from']) && trim($get['delivery_from'])!='' && isset($get['delivery_end']) && trim($get['delivery_end'])!=''){
  		
		$where_string.=" and delivery_at between '".$get['delivery_from']. "' and '" .$get['delivery_end'] ."  23:59:59'";
	}
	
	if(isset($get['pay_from']) && trim($get['pay_from'])!='' && isset($get['pay_end']) && trim($get['pay_end'])!=''){
		 
		$where_string.=" and  pay_at between '".$get['pay_from']. "' and '" .$get['pay_end']."  23:59:59'" ;
	
	}
	
	if(isset($get['create_from']) && trim($get['create_from'])!='' && isset($get['create_from']) && trim($get['create_end'])!=''){
 		
		$where_string.=" and create_time between '".$get['create_from']. "' and '" .$get['create_end']."  23:59:59'" ;
	}
	
	 
	return $where_string;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">订单列表</p>
<form id="order_search" name="order_search" method="get">
  <table width="100%" border="0" class="phpshop123_search_box">
    <tr>
      <td>订单序列号</td>
      <td><label>
        <input name="sn" type="text" id="sn" value="<?php echo isset($_GET['sn'])?$_GET['sn']:''; ?>" />
      </label></td>
      <td>订单状态</td>
      <td><label>
<select name="status" id="status">
		<option value="" <?php if(isset($_GET['status']) && $_GET['status']==''){ ?>selected<?php } ?>>不限制</option>

	<?php foreach($order_status as $key=>$value){ ?>
	<option value="<?php echo $key;?>" <?php if(isset($_GET['status']) && $_GET['status']==$key){ ?>selected<?php } ?>><?php echo $value;?></option>
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
      <td colspan="2"><label>
        <div align="right">
          <input type="submit" name="Submit" value="搜索" />
        </div>
      </label></td>
    </tr>
  </table>
  <p align="right">&nbsp;</p>
</form>
<?php if ($totalRows_orders > 0) { // Show if recordset not empty ?>
  <p class="phpshop123_title">订单列表</p>
  <table width="100%" border="1" align="center" class="phpshop123_list_box">
    <tr>
      <th>订单序列号</th>
      <th>用户</th>
      <th>总计</th>
      <th>运费</th>
      <th>状态</th>
      <th>创建的时间</th>
      <th>送货方式</th>
      <th>支付方法</th>
      <th>需要支票</th>
      <th>收货人</th>
      <th>操作</th>
    </tr>
      <?php do { ?>
          <tr>
            <td><a href="detail.php?recordID=<?php echo $row_orders['id']; ?>"><?php echo $row_orders['sn']; ?></a></td>
            <td><div align="center"><?php echo $row_orders['username']; ?>&nbsp; </div></td>
            <td><div align="center">￥<?php echo $row_orders['should_paid']; ?>&nbsp; </div></td>
            <td>￥<?php echo $row_orders['shipping_fee']; ?></td>
            <td><div align="center"><?php echo $order_status[$row_orders['order_status']]; ?>&nbsp; </div></td>
            <td><div align="center"><?php echo $row_orders['create_time']; ?>&nbsp; </div></td>
            <td><div align="center"><?php echo isset($shipping_method[$row_orders['shipping_method']])?$shipping_method[$row_orders['shipping_method']]:"未设置"; ?>&nbsp; </div></td>
            <td><div align="center"><?php echo $pay_methomd[$row_orders['payment_method']]; ?>&nbsp; </div></td>
            <td><div align="center"><?php echo $row_orders['invoice_is_needed']=='0'?"":"√"; ?>&nbsp; </div></td>
            <td><div align="center"><?php echo $row_orders['consignee_name']; ?>&nbsp; </div></td>
            <td> 
				
			  <div align="center">
			    <?php if($row_orders['order_status']==ORDER_STATUS_PAID  ){ ?>
			    <a href="delivery.php?id=<?php echo $row_orders['id']; ?>">发货</a>
		        <?php  } ?>
			    <?php if($row_orders['order_status']==ORDER_STATUS_RETURNED_APPLIED  ){ ?>
				    
			    <a href="return.php?id=<?php echo $row_orders['id']; ?>" onClick="return confirm('您确认要对这个订单进行退货标记吗？')">退货</a>
			    <?php  } ?>
		          <a onClick="return confirm('您是否确实要删除这条记录？')" href="remove.php?id=<?php echo $row_orders['id']; ?>">删除</a> </div></td>
          </tr>
          <?php } while ($row_orders = mysql_fetch_assoc($orders)); ?>
  </table>
  <br>
  <table border="0" width="50%" align="right">
    <tr>
      <td width="23%" align="center"><?php if ($pageNum_orders > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_orders=%d%s", $currentPage, 0, $queryString_orders); ?>" class="phpshop123_paging">第一页</a>
                <?php } // Show if not first page ?>      </td>
      <td width="31%" align="center"><?php if ($pageNum_orders > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_orders=%d%s", $currentPage, max(0, $pageNum_orders - 1), $queryString_orders); ?>" class="phpshop123_paging">前一页</a>
                <?php } // Show if not first page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_orders < $totalPages_orders) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_orders=%d%s", $currentPage, min($totalPages_orders, $pageNum_orders + 1), $queryString_orders); ?>" class="phpshop123_paging">下一页</a>
                <?php } // Show if not last page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_orders < $totalPages_orders) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_orders=%d%s", $currentPage, $totalPages_orders, $queryString_orders); ?>" class="phpshop123_paging">最后一页</a>
                <?php } // Show if not last page ?>      </td>
    </tr>
  </table>
  记录 <?php echo ($startRow_orders + 1) ?> 到 <?php echo min($startRow_orders + $maxRows_orders, $totalRows_orders) ?> (总共 <?php echo $totalRows_orders ?>）
  </p>
  
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_orders == 0) { // Show if recordset empty ?>
    暂无记录！
    <?php } // Show if recordset empty ?>
	
	<link rel="stylesheet" href="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.css">
	<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
	<script>
	 $(function() {
		$( "#delivery_from" ).datepicker({ dateFormat: 'yy-mm-dd' });
		$( "#delivery_end" ).datepicker({ dateFormat: 'yy-mm-dd' });
		$( "#create_from" ).datepicker({ dateFormat: 'yy-mm-dd' });
		$( "#create_end" ).datepicker({ dateFormat: 'yy-mm-dd' });
		$( "#pay_from" ).datepicker({ dateFormat: 'yy-mm-dd' });
		$( "#pay_end" ).datepicker({ dateFormat: 'yy-mm-dd' });
   });
	</script>
	
</body>

</html>
<?php
mysql_free_result($orders);
?>
