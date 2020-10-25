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

if(!isset($_SESSION['user_id'])){
	 $url="/login.php";
  	header("Location: " .  $url );
}
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


$cart_obj = new Cart ();
$cart = $cart_obj->get ();
$cart_products = $cart ['products'];

//	检查购物车是否有产品，如果没有产品的话，那么直接跳转到购物车页面
if(count($cart_products)==0){
	 $url="/cart.php";
  	header("Location: " .  $url );
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
    $insertSQL = sprintf("INSERT INTO user_consignee (user_id,name, mobile, province, city, district, address, zip) VALUES (%s,%s, %s, %s, %s, %s, %s, %s)",
  					   GetSQLValueString($_SESSION['user_id'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['mobile'], "text"),
                       GetSQLValueString($_POST['province'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['district'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['zip'], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
}

// 获取这个用户的所有的收货人信息
$colname_consignee = "-1";
if (isset($_SESSION['user_id'])) {
  $colname_consignee = (get_magic_quotes_gpc()) ? $_SESSION['user_id'] : addslashes($_SESSION['user_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_consignee = sprintf("SELECT * FROM user_consignee WHERE user_id = %s and is_delete=0 order by is_default desc", $colname_consignee);
$consignee = mysql_query($query_consignee, $localhost) or die(mysql_error());
$row_consignee = mysql_fetch_assoc($consignee);
$totalRows_consignee = mysql_num_rows($consignee);
 
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "order_form")) {
	
//		检查session中是否有收货人信息，如果没有设置的话，那么设置收货人信息
	
	//	如果只有1个收货人信息的话，那么默认就是这个
 	//	插入订单信息，().
	 
	$sn=date('YmdHis').rand(0,9999);
	$should_paid=$_SESSION['cart']['order_total'];
	$actual_paid="0.00";
    $insertSQL = sprintf("INSERT INTO orders (products_total,shipping_fee,sn, user_id, should_paid, actual_paid, shipping_method, payment_method, invoice_is_needed, invoice_title, invoice_message,please_delivery_at,consignee_id,consignee_name,consignee_province,consignee_city,consignee_district,consignee_address,consignee_zip,consignee_mobile) VALUES (%s,%s,%s,%s,%s, %s, %s, %s, %s, %s, %s,  %s, %s, %s, %s, %s, %s,%s, %s, %s)",
					   GetSQLValueString($_SESSION['cart']['products_total'], "double"),
 					   GetSQLValueString($_SESSION['cart']['shipping_fee'], "double"),
					   GetSQLValueString($sn, "text"),
                       GetSQLValueString($_SESSION['user_id'], "text"),
                       GetSQLValueString($should_paid, "text"),
                       GetSQLValueString($actual_paid, "text"),
                       GetSQLValueString($_SESSION['cart']['shipping_method_id'], "int"),
                       GetSQLValueString($_POST['payment_method'], "int"),
                       GetSQLValueString(isset($_POST['invoice_is_needed'])?1:0, "int"),
					   GetSQLValueString($_POST['invoice_title'], "text"),
					   GetSQLValueString($_POST['invoice_message'], "text"),
					   GetSQLValueString($_POST['please_delivery_at'], "int"),
                       GetSQLValueString($_POST['consignee_id'], "int"),
					   GetSQLValueString($_SESSION['consignee']['name'], "text"),
					   GetSQLValueString($_SESSION['consignee']['province'], "text"),
					   GetSQLValueString($_SESSION['consignee']['city'], "text"),
					   GetSQLValueString($_SESSION['consignee']['district'], "text"),
					   GetSQLValueString($_SESSION['consignee']['address'], "text"),
					   GetSQLValueString($_SESSION['consignee']['zip'], "text"),
					   GetSQLValueString($_SESSION['consignee']['mobile'], "text")
 					   );
  
   $Result1 = mysql_query($insertSQL) or die(mysql_error());
  	$order_id=mysql_insert_id();
  
  //	检查参数，如果参数不正确的话，能否告知？
  
  //	如果参数正确的话，那么进行数据插入
  foreach($cart_products as $product){
  	$sql="insert into order_item(attr_value,product_id,quantity,should_pay_price,actual_pay_price,order_id)values('".$product['attr_value']."','".$product['product_id']."','".$product['quantity']."','".$product['product_price']."','".$product['product_price']."','".$order_id."')";
  	mysql_query($sql);
  }
  
	//	如果插入成功，那么清空购物有车
	$cart_obj->clear();
	
	// 删除缓存中的收货人数据
	unset($_SESSION['consignee']);
	  
	//	如果插入失败，那么告知；
	
	//	记录进入日志操作
	  
	$order_log_sql="insert into order_log(order_id,message)values('".$order_id."','创建订单成功')";
	mysql_query($order_log_sql, $localhost);
	//	  如果成功，那么跳转到付款页面
	$MM_redirectLoginSuccess="payoff.php?order_sn=".$sn;
	header("Location: " . $MM_redirectLoginSuccess );
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>订单确认</title>
 <style>
 *{font-size:12px;font-family:"Microsoft Yahei"}
 table{
 	border-collapse:collapse;
 }
 hr{
 	width:948px;
 	border:none;
	border-top:1px solid #F0F0F0;
 }
.consignee_row:hover{
 	background-color:#fff3f3;
}
.consignee_row:hover .consignee_selector{
 	background-color:#ffffff;
	border-collapse:collapse;
}


.consignee_row:hover .consignee_op{
 	display:block;
}

.consignee_selector{
	font-size:12px;
}

.set_consingee:hover{
	cursor:pointer;
}

.consignee_selected{
	border:1px solid #FF0000;
}

.consignee_unselected{
	border:1px solid #FF0000;
}

.consignee_op{
	float:right;
	display:none;
}

.consignee_op a{
	text-decoration:none;
	color:#005ea7;
}
 .STYLE1 {font-weight: bold}
 .STYLE2 {color: #999999}
 </style>
</head>

<body style="margin:0px;">	
<?php 	include_once('widget/top_full_nav.php'); ?>
<?php  	include_once('widget/logo_step.php'); ?>
<div align="left" style="width:990px;margin:0 auto;height:42px;line-height:42px;font-size:16px;color:#666;">填写并核对订单信息</div>
<table width="990" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#F0F0F0">
  <tr>
    <td><div align="center">
      <table width="948" border="0" cellpadding="0">
         <tr>
          <td><strong>收货人信息</strong></td>
          <td height="40"><div align="right"><a style="color:#005ea7;text-decoration:none;" href="javascript://" onclick="return show_add_consignee_form();">新增收货地址</a></div></td>
        </tr>
		<tr>
		<td>
		<form action="<?php echo $editFormAction; ?>" method="post" name="add_consignee_form" id="add_consignee_form">
     <table align="center">
      <tr valign="baseline">
        <td nowrap align="right">姓名:</td>
        <td><input type="text" name="name" value="" size="32" >
        *</td>
      </tr>
      <tr valign="baseline">
        <td nowrap align="right">手机:</td>
        <td><input type="text" name="mobile" value="" size="32" >
        *</td>
      </tr>
      <tr valign="baseline">
        <td nowrap align="right">省市:</td>
        <td><?php include($_SERVER['DOCUMENT_ROOT'].'/widget/area/index.php');?></td>
      </tr>
      <tr valign="baseline">
        <td nowrap align="right" class="required">地址:</td>
        <td><input type="text" name="address" value="" size="32">
        *</td>
      </tr>
      <tr valign="baseline">
        <td nowrap align="right" class="required">邮编:</td>
        <td><input type="text" name="zip" value="" size="32">
        *</td>
      </tr>
      <tr valign="baseline">
        <td nowrap align="right">&nbsp;</td>
        <td><input type="submit" value="添加"></td>
      </tr>
    </table>
    <input type="hidden" name="MM_insert" value="form1">
  </form>
		</td>
		</tr>
        <tr >
          <td height="30" colspan="2"><table width="100%" border="0">
		   <?php if ($totalRows_consignee > 0) { $default_address=true;?>
		    <?php do { ?>
              <tr class="consignee_row">
                <td width="11%">
		    	<?php if($default_address==true || $totalRows_consignee==1){?>
				<?php 
					$_SESSION['consignee']['id']=$row_consignee['id'];
					$_SESSION['consignee']['name']=$row_consignee['name'];
					$_SESSION['consignee']['mobile']=$row_consignee['mobile'];
					$_SESSION['consignee']['province']=$row_consignee['province'];
					$_SESSION['consignee']['city']=$row_consignee['city'];
					$_SESSION['consignee']['district']=$row_consignee['district'];
					$_SESSION['consignee']['address']=$row_consignee['address'];
					$_SESSION['consignee']['zip']=$row_consignee['zip'];
				?>
				<table class="consignee_selector" id="consignee_selector_<?php echo $row_consignee['id'];?>"  width="98" height="30" border="2" cellpadding="0" cellspacing="0" bordercolor="#E4393C">
                  <tr>
                    <td width="98" height="30" bordercolor="#E4393C">
                    <div align="center" class="set_consingee" onclick="select_consignee(<?php echo $row_consignee['id'];?>)">默认地址</div>
                    </td>
                  </tr>
                </table>
                 <?php }else{?>
                 <table class="consignee_selector" id="consignee_selector_<?php echo $row_consignee['id'];?>"   width="98" height="30" border="1" cellpadding="0" cellspacing="0" bordercolor="#cccccc">
                  <tr>
                     <td width="98" height="30" bordercolor="#cccccc">
                     	<div align="center" class="set_consingee" onclick="select_consignee(<?php echo $row_consignee['id'];?>)"><?php echo $row_consignee['name']; ?> <?php echo $row_consignee['province']; ?></div>
                     </td>
                   </tr>
                </table>
                     <?php 	}?>
                    <?php $default_address=false;?>
                </td>
                <td width="89%">
 				<span class="consignee_name" id="consignee_name_<?php echo $row_consignee['id']; ?>"><?php echo $row_consignee['name']; ?></span>
				<span class="consignee_province" id="consignee_province_<?php echo $row_consignee['id']; ?>"><?php echo $row_consignee['province']; ?> </span>
				<span class="consignee_city" id="consignee_city_<?php echo $row_consignee['id']; ?>"> <?php echo $row_consignee['city']; ?></span>
				<span class="consignee_district" id="consignee_district_<?php echo $row_consignee['id']; ?>">  <?php echo $row_consignee['district']; ?> </span>
				<span class="consignee_address" id="consignee_address_<?php echo $row_consignee['id']; ?>">  <?php echo $row_consignee['address']; ?> </span>
				<span class="consignee_zip" id="consignee_zip_<?php echo $row_consignee['id']; ?>">  <?php echo $row_consignee['zip']; ?> </span>
				
				<span class="consignee_mobile" id="consignee_mobile_<?php echo $row_consignee['id']; ?>">  <?php echo $row_consignee['mobile']; ?></span>
				<div class="consignee_op" onhover="toggle_consignee_op(<?php echo $row_consignee['id']; ?>)">
					<a href="consignee_default.php?id=<?php echo $row_consignee['id']; ?>">设为默认</a>
					<a href="consignee_del.php?id=<?php echo $row_consignee['id']; ?>" onclick="return confirm('您确定要删除这个收货地址吗？');">删除</a>
					<a href="/user/index.php?path=consignee/update.php?id=<?php echo $row_consignee['id']; ?>">更新</a>
				</div>
				</td>
              </tr>
			   <?php } while ($row_consignee = mysql_fetch_assoc($consignee)); ?>
			  <?php } ?>
             </table>
          </td>
        </tr>
      </table>
      <hr width="98%" noshade="noshade" />
	  
	<form id="order_form" name="order_form" method="post" onSubmit="return check_consignee();">

      <table width="948" border="0" cellpadding="0">
        <tr>
          <td height="40"><strong>支付方式</strong></td>
        </tr>
        <tr>
          <td height="40"><table width="98" height="30" border="2" cellpadding="0" cellspacing="0" bordercolor="#E4393C">
            <tr>
              <td width="98" height="30" bordercolor="#E4393C"><div align="center">在线支付</div></td>
            </tr>
          </table></td>
        </tr>
      </table>
      <hr width="98%" noshade="noshade" />
      <table width="948" border="0" cellpadding="0">
        <tr>
          <td height="40"><strong>送货清单</strong></td>
        </tr>
        <tr>
          <td height="184" valign="top">
		  <table width="948" border="0" cellpadding="0">
            <tr>
              <td width="330" height="187" valign="top" bgcolor="#f7f7f7"><table width="100%" border="0" cellpadding="0">
                <tr>
                  <td height="24">配送方式</td>
                </tr>
                <tr>
                  <td height="50"><table width="145" height="30" border="2" cellpadding="0" cellspacing="0" bordercolor="#E4393C">
  <tr>
    <td width="145" height="30" bordercolor="#E4393C"><div align="center">快递</div></td>
  </tr>
</table>
</td>
                </tr>
                <tr>
                  <td height="33">&nbsp;</td>
                </tr>
                <tr>
                  <td></td>
                </tr>
              </table></td>
              <td height="187" valign="top" bgcolor="#f3fbfe"><table width="576" height="102" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="24" colspan="5">商家：自营</td>
                </tr>
                 <?php
					foreach ( $cart_products as $cart_products_item ) {
						if(!isset($cart_products_item ['product_name'])){
							continue;
						}	
						mysql_select_db($database_localhost, $localhost);
						$query_product_image = "SELECT * FROM product_images WHERE product_id = ".$cart_products_item['product_id'];
						$product_image = mysql_query($query_product_image, $localhost) or die(mysql_error());
						$row_product_image = mysql_fetch_assoc($product_image);
						$totalRows_product_image = mysql_num_rows($product_image);
			
					?>
				<tr>
                  <td height="102" rowspan="2" align="center" valign="top"><a href="/product.php?id=<?php echo $cart_products_item['product_id'];?>"><img style="border:1px solid #ddd;padding:1px;" src="<?php echo $row_product_image['image_files']==NULL?"/uploads/default_product.png":$row_product_image['image_files']; ?>" alt="正在下载..." width="82" height="82" /></a></td>
                  <td width="240" height="102" valign="top"><?php
					echo $cart_products_item ['product_name'];
					?><br /><?php
					echo str_replace(";"," ",$cart_products_item ['attr_value']);
					?></td>
                  <td width="100" height="102" valign="top"><span style="color:#FF0000;font-weight:bold;">￥<?php
					echo $cart_products_item ['product_price'];
					?></span></td>
                  <td width="70" height="102" valign="top">X<?php
					echo $cart_products_item ['quantity'];
					?></td>
                  <td height="102" valign="top">有货</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td colspan="3">&nbsp;</td>
                </tr>
 				  <?php  } ?>
              </table></td>
            </tr>
           </table></td>
        </tr>
      </table>
      <table width="948" border="0" cellpadding="0" align="center">
        <tr>
          <td height="38"><hr width="98%" noshade="noshade" />
            <p><strong>发票信息</strong></p></td>
        </tr>
        <tr>
          <td height="38" align="left"><p>需要发票：
            <input name="invoice_is_needed" type="checkbox" id="invoice_is_needed " />
            </p>
            <p>发票抬头： 
              <input name="invoice_title" type="text" id="invoice_title " />
            </p>
            <p>发票备注：
              <input name="invoice_message" type="text" id="invoice_message " />
            </p></td>
        </tr>
		
		<tr>
        <td align="left">  <p><strong>收货时间：</strong>     
        <select name="please_delivery_at" id="please_delivery_at">
          <option value="1">每天都可以</option>
          <option value="2">工作日可以</option>
          <option value="3">周六周日可以</option>
        </select></p>
		 <input name="payment_method" type="hidden" value="100" />
		</td>
      </tr>
  </table>
      </div></td>
  </tr>
</table>
<table width="990" height="116" border="0" align="center" cellpadding="0">
  <tr>
    <td><div align="right">件商品，总商品金额：</div></td>
    <td width="100"><div align="right">￥<?php echo  $_SESSION['cart']['products_total'];?></div></td>
  </tr>
  <tr>
    <td><div align="right">返现：</div></td>
    <td><div align="right">-￥0.00</div></td>
  </tr>
  <tr>
    <td><div align="right">运费：</div></td>
    <td><div align="right">￥<?php echo  $_SESSION['cart']['shipping_fee'];?></div></td>
  </tr>
  <tr>
    <td><div align="right"> 应付总额：</div></td>
    <td><div align="right">￥<?php echo  $_SESSION['cart']['order_total'];?></div></td>
  </tr>
</table>
<hr align="center" width="990" />
<table width="990" border="0" align="center" cellpadding="0">
  <tr>
    <td height="90"><div align="right">
      <span style="font-size:14px;">应付总额：</span><span style="color:#e4393c;font-size:20px;font-weight:700;">￥<?php echo  $_SESSION['cart']['order_total'];?></span>
	  <?php 
	  	$could_deliver=false;
 		$areas[]=trim($_SESSION['consignee']['province'])."_*_*";
		$areas[]=trim($_SESSION['consignee']['province'])."_".trim($_SESSION['consignee']['s'])."_*";
		$areas[]=trim($_SESSION['consignee']['province'])."_".trim($_SESSION['consignee']['city'])."_".trim($_SESSION['consignee']['district']);
 	  	if(could_devliver($areas)){
	  ?>
          <input id="new_order_button" style="margin-left:10px;border-radius:4px;width:135px;height:36px;line-height:20px;border:0px;background-color:#FF0000;color:white;font-size:20px;" type="submit" name="Submit" value="提交" />
		  <?php }else{ ?>
		  
		  <input id="new_order_button" style="margin-left:10px;border-radius:4px;width:135px;height:36px;line-height:20px;border:0px;background-color:#FF0000;color:white;font-size:20px;" type="submit" name="Submit" value="此地址无货" disabled="true"/>
		  <?php } ?>
		  <input name="shipping_method" type="hidden" id="shipping_method" value="100" />
		  <input name="MM_insert" type="hidden" id="MM_insert" value="order_form" />
     	<input name="consignee_id" type="hidden" id="consignee_id" value="" />
     </div></td>
  </tr>
</table>
</form>
<table width="990" border="0" align="center" cellpadding="0" bgcolor="#F4F4F4">
  <tr>
    <td height="110">
	<div align="right">
      <table width="100%" border="0">
        <tr><td height="40"></td></tr>
       </table>
    </div></td>
  </tr>
</table>
</body>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script>
$().ready(function(){

 		$("#add_consignee_form").hide();
 
	
   	$("#add_consignee_form").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
				maxlength: 10
            },
            mobile: {
                required: true,
                minlength: 11 ,
				maxlength: 11,
				digits:true
            },
            address: {
                required: true,
                minlength: 3 ,
				maxlength: 30
            },
            zip: {
                required: true,
                minlength: 6,
                maxlength: 30,
				digits:true
            }
        },
        messages: {
            name: {
                required: "必填",
                minlength: "最起码2个汉字起哦",
				maxlength: "最多只能10个汉字哦"
            },
            mobile: {
                required: "必填",
                minlength: "只能11位",
				maxlength: "只能11位",
				digits:"只能是数字哦"
            },
            address: {
                required: "必填",
                minlength: "3个汉字起哦",
				maxlength: "最多只能30个汉字哦"
            },
            zip: {
                required: "必填",
                minlength: "只能是6位哦",
				maxlength: "只能是6位哦",
				digits:"只能是数字哦"
            }
        }
    });
 });
function show_add_consignee_form(){
	$("#add_consignee_form").toggle('slow');
}

function select_consignee(id){
	var url="/consignee_select.php";
	var consignee_id=id;
 	var consignee_name=$("#consignee_name_"+id).html();
	var consignee_mobile=$("#consignee_mobile_"+id).html();
	var consignee_province=$("#consignee_province_"+id).html();
	var consignee_city=$("#consignee_city_"+id).html();
	var consignee_district=$("#consignee_district_"+id).html();
	var consignee_address=$("#consignee_address_"+id).html();
	var consignee_zip=$("#consignee_zip_"+id).html();
	
	$.post(url,{consignee_id:consignee_id,consignee_name:consignee_name,consignee_mobile:consignee_mobile,consignee_province:consignee_province,consignee_city:consignee_city,consignee_district:consignee_district,consignee_address:consignee_address,consignee_zip:consignee_zip},function(data){
		if(data.code=="0"){
			//		更新ui
			_set_consignee_as_selected(id); // 将这个收货人地址设为选中
			_set_consignee_as_unselected(id);	//	将其余收货人地址设为未选中
			if(data.could_deliver=="0"){
				$("#new_order_button").attr("disabled","true");
				$("#new_order_button").val("此地址无货");
			}else{
				$("#new_order_button").removeAttr("disabled");
				$("#new_order_button").val("提交");
			}	
			return;
		}
		alert(data.message);return;
	},'json');
}

function _set_consignee_as_selected(id){
  	$("#consignee_selector_"+id).attr("borderColor","#E4393C");
	$("#consignee_selector_"+id).attr("border","2");
}

function _set_consignee_as_unselected(id){

 	//		循环所有的table，只要不是这个id的，都设置为
	 $(".consignee_selector").each(function(){
   			 if($(this).attr('id')!="consignee_selector_"+id){
			 	$(this).attr("borderColor","#cccccc");
				$(this).attr("border","1");
			 }
  		});
	 
}
function _set_consignee_id(id){
}
function toggle_consignee_op(id){

}

function check_consignee(){
 
 	if($(".consignee_row").length>0){
		return true;
	}
	
	alert('请至少添加一个收货地址。');
	$("#add_consignee_form").show('slow');
	return false;
}

</script>
</html>