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
<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/Connections/localhost.php');
?>
<?php

$cart_obj = new Cart ();
if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$cart_obj->add ( $_POST );
}

$cart = $cart_obj->get ();
$cart_products = $cart ['products'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>123phpshop-购物车</title>
<style type="text/css">
<!--
table {
	border-collapse: collapse;
}

a {
	text-decoration: none;
	color: black;
}

a:hover {
	color: red;
}

.STYLE1 {
	font-size: 12px
}

.STYLE3 {
	color: #999;
	font-size: 12px;
}

.STYLE4 {
	color: #FF0000;
	font-size: 16px;
	font-weight: bold;
}

.STYLE5 {
	color: #FF0000;
	font-weight: bold;
}

#empty_cart {
	text-align: center;
	width: 990px;
	margin: 0 auto;
	font-size: 25px;
}
-->
</style>
<link href="css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body style="margin: 0px;">
<?php
include_once ('widget/top_full_nav.php');
?>
<?php

include_once ('widget/logo_search.php');
?>
<p>
  <?php
		if (empty ( $cart_products )) {
			?>
</p>
<div id="empty_cart">
<div align="center">
<p><a href="index.php"><img src="uploads/default_product.png" alt="123phpshop.com" width="350" height="350" /></a></p>
<p><a href="index.php">购物车里面空空如也，赶紧把他填满吧。</a></p>
</div>
</div>
<?php
		} else {
			?>
 <form id="cart_form" name="cart_form" method="post" action="confirm.php">
<table width="990" height="37" border="0" align="center">
	<tr>
		<td height="37"><span class="STYLE5">全部商品 </span></td>
	</tr>
</table>
<table width="990" border="0" align="center" cellpadding="0"
	cellspacing="0" bordercolor="#666">
	<tr>
		<td width="133" height="43" bgcolor="#f3f3f3" scope="col">&nbsp;</td>
		<td width="190" bgcolor="#f3f3f3" scope="col"><span class="STYLE1">商品</span></td>
		<td width="172" height="43" bgcolor="#f3f3f3" scope="col"><span
			class="STYLE1">单价（元）</span></td>
		<td width="169" height="43" bgcolor="#f3f3f3" scope="col"><span
			class="STYLE1">数量</span></td>
		<td width="140" height="43" bgcolor="#f3f3f3" scope="col"><span
			class="STYLE1">小计（元）</span></td>
		<td width="186" height="43" bgcolor="#f3f3f3" scope="col"><span
			class="STYLE1">操作</span></td>
	</tr>
</table>
<table style="font-size: 12px;" width="990" border="1" align="center"
	cellpadding="0" cellspacing="0" bordercolor="#fff4e8">
    <?php
			foreach ( $cart_products as $cart_products_item ) {
				if (isset ( $cart_products_item ['product_id'] )) {
					?>
    <tr bgcolor="#fff4e8"
		id="product_<?php
					echo $cart_products_item ['product_id'];
					?>">
		<td width="133" height="107" >
		<div align="center"><a style="border:0px;"
			href="product.php?id=<?php
					echo $cart_products_item ['product_id'];
					?>"><img  style="border:0px;"
			src="<?php
					echo $cart_products_item ['product_image'] != null ? $cart_products_item ['product_image'] : "/uploads/default_product.png";
					?>"
			width="80" height="80" /></a></div>
		</td>
		<td width="184" valign="middle"><a
			href="product.php?id=<?php
					echo $cart_products_item ['product_id'];
					?>">
	    <?php
					echo $cart_products_item ['product_name'];
					?> <br />
	    <?php
					echo str_replace(";"," ",$cart_products_item ['attr_value']);
					?>	    
	    &nbsp;</a></td>
		<td width="171" height="107"><span
			class="product_price_<?php
					echo $cart_products_item ['product_id'];
					?>" attr_value="<?php
					echo $cart_products_item ['attr_value'];
					?>"><?php
					echo $cart_products_item ['product_price'];
					?></span></td>
		<td width="178" height="107">
		 
		<div name="increase_quantity" style="cursor:pointer;float:left;height:20px;line-height:20px;width:20px;border:1px solid #e54346;background-color:red;color:#FFFFFF;text-align:center;"
			onclick="return change_quantity(<?php
					echo $cart_products_item ['product_id'];
					?>,1,'<?php
					echo $cart_products_item ['attr_value'];
					?>')"
			id="increase_quantity_product_quantity_<?php
					echo $cart_products_item ['product_id'];
					?>">+</div>
		<input readOnly="true"  style="float: left; text-align: center;height:18px;line-height:18px;border:1px solid #e54346;border-left:0px;border-right:0px;margin-top:0px;"
			class="product_quantity_<?php
					echo $cart_products_item ['product_id'];
					?>"
			value="<?php
					echo $cart_products_item ['quantity'];
					?>"
			size="2" maxlength="10" attr_value="<?php
					echo $cart_products_item ['attr_value'];
					?>"/>
		<div height="15" width="15"   name="decrease_quantity" style="cursor:pointer;line-height:20px;border:1px solid #e54346;float: left;height:20px;width:20px;background-color:red;color:#FFFFFF;backgroun-color:red;color:#FFFFFF;text-align:center;"
			onclick="return change_quantity(<?php
					echo $cart_products_item ['product_id'];
					?>,-1,'<?php
					echo $cart_products_item ['attr_value'];
					?>')"
			id="decrease_quantity_product_quantity_<?php
					echo $cart_products_item ['product_id'];
					?>">-</div>
					 
		</td>
		<td width="140" height="107"><strong
			class="sub_total_<?php
					echo $cart_products_item ['product_id'];
					?>" attr_value="<?php
					echo $cart_products_item ['attr_value'];
					?>"><?php
					echo $cart_products_item ['quantity'] * $cart_products_item ['product_price'];
					?></strong></td>
		<td width="170" height="107"><a href="javascript://"
			onClick="delete_cart_product(<?php
					echo $cart_products_item ['product_id'];
					?>,'<?php
					echo $cart_products_item ['attr_value'];
					?>');">删除</a></td>
	</tr>
    <?php
				}
			}
			?>
  </table>
<table width="990" height="50" border="1" align="center" cellpadding="0"
	cellspacing="0" bordercolor="#ddd">
	<tr>
		<td>
		<table width="187" border="0" align="right">
			<tr>
				<td><span class="STYLE3">总价(不含运费)：<span class="STYLE4">￥<span
					id="cart_total_price"><?php
			echo $cart ['products_total'];
			?></span></span></span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>
		</td>
		<td width="96" bgcolor="#e54346"><input
			style="font-weight: bold; border: 1px solid #e54346; font-size: 18px; background-color: #e54346; color: white; height: 48px; width: 98px;"
			type="submit" name="Submit" value="去结算" /></td>
	</tr>
</table>
</form>


<?php
		}
		?>
<?php

		include_once ('widget/footer.php');
		?>
<script language="JavaScript" type="text/javascript"
	src="/js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript"
	src="/js/jquery.validate.min.js"></script>
<script>
function delete_cart_product(product_id,attr_value){
	
	if(!confirm("您确实要删除此商品么？")){
		return false;
	}
	
	var url="/ajax_remove_cart_product.php";
	$.post(url,{product_id:product_id,attr_value:attr_value},function(data){
		if(data.code=='0'){
			location.href="/cart.php"
			return true;
		}
		alert(data.message);return false;
	},'json');
}

 var change_quantity=function(product_id,quantity,attr_value){
	
	var now_quantity=$(".product_quantity_"+product_id+"[attr_value='"+attr_value+"']").val();
 //			获取box中的产品数量的值。如果产品数量为1，但是需要减去一个话，那么告知需要最少要有一件产品
	if(now_quantity==1 && quantity==-1){
		alert("至少需要留1件商品,如果需要删除这个商品，请点击旁边的删除按钮");return false;
	}

//	更新这个产品的数量
	var final_quantity=parseInt($(".product_quantity_"+product_id+"[attr_value='"+attr_value+"']").val())+parseInt(quantity);
	$(".product_quantity_"+product_id+"[attr_value='"+attr_value+"']").val(final_quantity);

//	调用ajax文件进行更新
 	$.post('/ajax_adjust_cart_quantity.php',{product_id:product_id,quantity:final_quantity,attr_value:attr_value},function(data){
	if(data.code!="0"){
		alert(data.message);return false;
	}

//		更新总价
	_update_total_price(data.data.total_price);
	_update_sub_total(product_id,attr_value);
	return false;
 	},'json');
 	return false;
}

function _update_total_price(total_price){
	$("#cart_total_price").html(total_price);
}
function _update_sub_total(product_id,attr_value){
	//获取产品的id
	var quantity=parseInt($(".product_quantity_"+product_id+"[attr_value='"+attr_value+"']").val());
	var price=parseFloat($(".product_price_"+product_id+"[attr_value='"+attr_value+"']").html()).toFixed(2);
 	var sub_total=parseFloat(quantity*price).toFixed(2);
 	$(".sub_total_"+product_id+"[attr_value='"+attr_value+"']").html(sub_total);
}

</script>
</body>
</html>
