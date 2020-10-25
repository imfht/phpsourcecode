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
<?php require_once('Connections/lib/product.php'); ?>
<?php
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

$colname_product = "-1";
if (isset($_GET['id'])) {
  $colname_product = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
  
mysql_select_db($database_localhost, $localhost);
$query_product = sprintf("SELECT product.*,brands.name as brand_name FROM product left join brands on product.brand_id=brands.id WHERE product.id = %s and product.is_delete=0 and product.is_on_sheft=1", $colname_product);
$product = mysql_query($query_product, $localhost) or die(mysql_error());
$row_product = mysql_fetch_assoc($product);
$totalRows_product = mysql_num_rows($product);
if($totalRows_product==0){
		$remove_succeed_url="/";
		header("Location: " . $remove_succeed_url );
}
$colname_product_images = "-1";
if (isset($_GET['id'])) {
  $colname_product_images = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_product_images = sprintf("SELECT * FROM product_images WHERE product_id = %s  and is_delete=0", $colname_product_images);
$product_images = mysql_query($query_product_images, $localhost) or die(mysql_error());
$row_product_images = mysql_fetch_assoc($product_images);
$totalRows_product_images = mysql_num_rows($product_images);

$colname_product_image_small = "-1";
if (isset($_GET['id'])) {
  $colname_product_image_small = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_product_image_small = sprintf("SELECT * FROM product_images WHERE product_id = %s", $colname_product_image_small);
$product_image_small = mysql_query($query_product_image_small, $localhost) or die(mysql_error());
$row_product_image_small = mysql_fetch_assoc($product_image_small);
$totalRows_product_image_small = mysql_num_rows($product_image_small);


$could_deliver=false;
 
$areas=get_deliver_areas();


$colname_consignee = "-1";
if (isset($_SESSION['user_id'])) {
  $colname_consignee = (get_magic_quotes_gpc()) ? $_SESSION['user_id'] : addslashes($_SESSION['user_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_consignee = sprintf("SELECT * FROM user_consignee WHERE user_id = %s and is_delete=0 and is_default=1", $colname_consignee);
$consignee = mysql_query($query_consignee, $localhost) or die(mysql_error());
$row_consignee = mysql_fetch_assoc($consignee);
$totalRows_consignee = mysql_num_rows($consignee);
$areas=array();
if($totalRows_consignee>0 && !isset($_SESSION['user']['province']) && !isset($_SESSION['user']['city']) && !isset($_SESSION['user']['district']) ){
	
	$areas[]=$row_consignee['province']."_*_*";
	$areas[]=$row_consignee['province']."_".$row_consignee['city']."_*";
	$areas[]=$row_consignee['province']."_".$row_consignee['city']."_".$row_consignee['district'];
}else{
 	$areas[]=$_SESSION['user']['province']."_*_*";
	$areas[]=$_SESSION['user']['province']."_".$_SESSION['user']['city']."_*";
	$areas[]=$_SESSION['user']['province']."_".$_SESSION['user']['city']."_".$_SESSION['user']['district'];
}

 $could_deliver=could_devliver($areas);

mysql_select_db($database_localhost, $localhost);
$query_product_catalog = "SELECT id,name FROM `catalog` WHERE id = ".$row_product['catalog_id'];
$product_catalog = mysql_query($query_product_catalog, $localhost) or die(mysql_error());
$row_product_catalog = mysql_fetch_assoc($product_catalog);
$totalRows_product_catalog = mysql_num_rows($product_catalog);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $row_product['name']; ?>|<?php echo $row_product['ad_text']; ?></title>
<link rel="stylesheet" type="text/css" href="/js/jquery-ui-1.11.4.custom/jquery-ui.css">
<style type="text/css">
body{
	font-family:'microsoft yahei';
	font-weight:normal;
}

table{
	border-collapse:collapse;
	
}
body {
 	font-size:15px;
}
.all_cata_box {
	color: #FFFFFF;
	font-weight: bold;
	margin-left:10px;
}

#to_all_cata_link{
	
	text-decoration:none;
	color:#ffffff;
	
}
.product_title {
	font-size: 16px;
	font-weight: bold;
	line-height:1.5em;
 }
.ad_text {
	font-size: 14px;
	color: #FF0000;
	font-weight:normal;
	line-height:20px;
}
.STYLE7 {color: #FF0000}
.intro_cons_comment {font-size: 14px;text-decoration:none;color:#666666;line-height:30px;font-weight:normal;}
.STYLE9 {
	font-size: 18px;
	font-weight: bold;
}
</style>

</head>
<body style="margin:0px;">
<?php include_once('widget/top_full_nav.php'); ?>
<?php include_once('widget/logo_search_cart.php'); ?>

<form id="add_to_cart"  name="add_to_cart" method="post" action="cart.php">
 
  <table width="1210" border="0" align="center" cellpadding="0" cellspacing="0" style="margin:0px auto;">
    <tr valign="middle">
      <td width="210" align="center" bgcolor="#B1191A"><a id="to_all_cata_link" href="/catalogs.php">
      <div align="left"><span class="all_cata_box">全部分类</span></div></a></td>
      <td height="44" bgcolor="#FFFFFF">&nbsp&nbsp首页</td>
    </tr>
  </table>
  <hr align="center" style="border:1px solid #B1191A;margin:0px;"  width="100%" noshade="noshade" />
   <div style="background-color: #f2f2f2;margin-top:0px;border-bottom:1px solid #f2f2f2;">
  <table width="1210" height="45" border="0" align="center" style="margin:0px auto;" >
    <tr>
      <td><span class="STYLE9"><?php echo $row_product_catalog['name']; ?></span>  &gt; <?php echo $row_product['name']; ?></td>
    </tr>
  </table>
   <table width="1210" height="425"  border="0" align="center" bgcolor="#FFFFFF" style="margin:0px auto;" >
      <tr>
        <td width="31%" rowspan="7" align="center" valign="top" scope="row">
           <?php include_once('widget/product_image_slide/index.php'); ?>
        </td>
        <td valign="top">
		<div align="left">
          <p class="">&nbsp;</p>
          <p class="product_title"><?php echo $row_product['name']; ?></p>
          <p class="ad_text"><?php echo $row_product['ad_text']; ?></p>
        </div>
		<table width="98%">
		<tr>
      <td width="15%" height="40" bgcolor="#f7f7f7" scope="row"><blockquote>
        <p style="margin-left:12px;"> 本店价:</p>
      </blockquote></td>
      <td width="46%" height="40" bgcolor="#f7f7f7" scope="row"><div align="left"><span class="STYLE7"><strong id="jd-price">￥</strong><?php echo $row_product['price']; ?></span> </div></td>
      <td width="39%" bgcolor="#f7f7f7" scope="row"><div align="right"></div></td>
    </tr>
    <tr>
      <td height="38" scope="row"><blockquote>
        <p  style="margin-left:12px;">市场价：</p>
      </blockquote></td>
      <td height="38" colspan="2" scope="row"><div align="left"><s><strong id="jd-price">￥</strong><?php echo $row_product['market_price']; ?></s></div></td>
    </tr>
    <tr>
      <td height="38" scope="row"  style="padding-left:12px;">库&nbsp;&nbsp;&nbsp;&nbsp;存:</td>
      <td height="38" colspan="2" scope="row"><?php echo $row_product['store_num']; ?></td>
    </tr>
    <tr>
      <td height="38" scope="row" style="padding-left:12px;">品&nbsp;&nbsp;&nbsp;&nbsp;牌:</td>
      <td height="38" colspan="2" scope="row"><?php echo $row_product['brand_name']==""?"未设置":$row_product['brand_name']; ?></td>
    </tr>
    <tr>
      <td height="38" scope="row" style="padding-left:12px;">配送至:</td>
      <td height="38" colspan="2" valign="middle" scope="row" style="padding:0px;"><?php include_once('widget/area/index.php')?>  
	  <span id="could_deliver" <?php if(!$could_deliver){ ?>style="color:red;"<?php } ?>><?php if($could_deliver){ ?>有货<?php }else{ ?>无货<?php } ?></span></td>
    </tr>
	       <?php include_once('widget/product/single_choose_attr.php'); ?>
    <tr>
      <td scope="row" style="padding-left:12px;">      数&nbsp;&nbsp;&nbsp;&nbsp;量:</td>
      <td colspan="2" scope="row"><label>
  		 
      </label>
        <div align="left" style="height:32px;">
          <div id="up_quantity" onClick="return change_quantity(1)" style="cursor:pointer;text-align:center;width:32px;height:32px;line-height:30px;font-size:20px;float:left;border:0px;background-color:#e4393c;color:#FFFFFF;">+</div>
          <input readOnly="true" name="quantity" type="text" id="quantity" value="1" size="6" style="font-size:20px;height:30px;line-height:29px;text-align:center;float:left;border:1px solid #e4393c;;"/>
          <div id="down_quantity" onClick="return change_quantity(-1)" style="cursor:pointer;text-align:center;line-height:30px;font-size:20px;width:32px;height:32px;float:left;border:0px;background-color:#e4393c;color:#FFFFFF;">-</div>
        </div>
        <label>
        <div align="left">
          <input type="hidden" name="product_id" />  <input type="hidden" value="<?php echo trim($attr_value);?>" name="attr_value"  id="attr_value"/>
          <input name="product_name" type="hidden" id="product_name" value="<?php echo $row_product['name']; ?>" />
          <input name="product_image" type="hidden" id="product_image" value="<?php echo $row_product_images['image_files']; ?>" />
          <input name="ad_text" type="hidden" id="ad_text" value="<?php echo $row_product['ad_text']; ?>" />
          <input name="product_id" type="hidden" id="product_id" value="<?php echo $row_product['id']; ?>" />
        </div>        </label></td>
    </tr>
     <tr>
      <th height="231" colspan="3" align="left" scope="row"><label>
        
		<input style="margin-left:12px;cursor:pointer;border:1px solid #e4393c;color:#FFFFFF;font-weight:bold;border-radius:5px;height:38px;width:137px;background-color:#e4393c;border：1px solid #e4393c;<?php if($row_product['store_num']<=0 || $could_deliver==false){ ?>display:none;<?php } ?>" type="submit" name="Submit2" value="加入购物车" id="could_buy_button" onclick="return check_add_to_cart(this);"/>
 		 <div  id="could_not_buy_button" style="border:1px solid #CCCCCC;font-weight:bold;text-align:center;height:38px;line-height:36px;width:137px;margin-left:12px;background-color:#CCCCCC;<?php if($row_product['store_num']>0  && $could_deliver==true){ ?>display:none;<?php } ?>" onclick="return false;">库存不足</div> 		 
      </label></th>
      </tr>
		</table>
		</td>
      </tr>
    
  </table>
</form>
   </div>
  <table width="1210" border="0" align="center" style="margin:0px auto;">
    <tr>
      <td width="210" valign="top"> 
       <?php include_once('widget/view_buy.php'); ?>
      </td>
      <td valign="top" align="center"> 
         <table width="990" height="30" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#fff" style="border-collapse:collapse;border:1px solid #DEDFDE;border-top:2px solid #999999;margin:20px 0px;;" valign="top">
          <tr id="tabs">
			<th width="77" height="33" scope="col"><span class="intro_cons_comment">商品介绍</span></th>
			<th width="77" height="33" scope="col"><a href="#attr_list" class="intro_cons_comment">规格参数</th>
			<th width="77" height="33" scope="col"><a href="#consult" class="intro_cons_comment">咨询</a></th>
			<th width="77" height="33" scope="col"> <a href="#comment_list" class="intro_cons_comment">评价</a> </th>
			<td height="33"><span class="STYLE8"></span></td>
          </tr>
        </table>
  		<table width="990" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#DEDFDE" bgcolor="#FFFFFF" style="margin:0px auto;">
        <tr>
          <th bordercolor="#DEDFDE" scope="col"><?php echo $row_product['intro']; ?></th>
          </tr>
      </table >
	 <?php include('widget/product/attrs.php'); ?> 
 	 <?php include('widget/product/product_comment.php'); ?> 
	 <?php include('widget/product/product_consult.php'); ?> 
	</td>
</tr>
</table>
<script language="JavaScript" type="text/javascript" src="js/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="/js/product_image_slide/js/pic_tab.js"></script>
<script>
 <?php if($row_consignee['province']!='' && !isset($_SESSION['user']['province']) && !isset($_SESSION['user']['city']) && !isset($_SESSION['user']['district'])){ ?>
addressInit('province', 'city', 'district', '<?php echo $row_consignee['province']; ?>', '<?php echo $row_consignee['city']; ?>', '<?php echo $row_consignee['district']; ?>');
 <?php } else{?>
 addressInit('province', 'city', 'district', '<?php echo $_SESSION['user']['province']; ?>', '<?php echo $_SESSION['user']['city']; ?>', '<?php echo $_SESSION['user']['distict']; ?>');
  <?php }?>
jq('#demo1').banqh({
	box:"#demo1",//总框架
	pic:"#ban_pic1",//大图框架
	pnum:"#ban_num1",//小图框架
	prev_btn:"#prev_btn1",//小图左箭头
	next_btn:"#next_btn1",//小图右箭头
	pop_prev:"#prev2",//弹出框左箭头
	pop_next:"#next2",//弹出框右箭头
	prev:"#prev1",//大图左箭头
	next:"#next1",//大图右箭头
	pop_div:"#demo2",//弹出框框架
	pop_pic:"#ban_pic2",//弹出框图片框架
	pop_xx:".pop_up_xx",//关闭弹出框按钮
	mhc:".mhc",//朦灰层
	autoplay:true,//是否自动播放
	interTime:5000,//图片自动切换间隔
	delayTime:400,//切换一张图片时间
	pop_delayTime:400,//弹出框切换一张图片时间
	order:0,//当前显示的图片（从0开始）
	picdire:true,//大图滚动方向（true为水平方向滚动）
	mindire:true,//小图滚动方向（true为水平方向滚动）
	min_picnum:5,//小图显示数量
	pop_up:true//大图是否有弹出框
})
 
var change_quantity=function(quantity){
 
 	var now_quantity=jq("#quantity").val();
//			获取box中的产品数量的值。如果产品数量为1，但是需要减去一个话，那么告知需要最少要有一件产品
	if(now_quantity==1 && quantity==-1){
		alert("至少需要留1件商品哦");return false;
	}
	
	var final_result=parseInt(jq("#quantity").val())+parseInt(quantity);
	jq("#quantity").val(final_result);
 	return false; 
  
}
  
$("#city").change(function(){
	_check_deliver();
});
$("#province").change(function(){
	_check_deliver();
});

$("#district").change(function(){
	_check_deliver();
});

function check_add_to_cart(that){
	return $(that).css('display')!="none";
}

function _check_deliver(){
	var url="/ajax_check_deliver.php";
	var province=$("#province").val();
	var city=$("#city").val();
	var district=$("#district").val();
	var idata=new Array();
 	idata.push(province+"_*_*");
	idata.push(province+"_"+city+"_*");
	idata.push(province+"_"+city+"_"+district);
	 $.post(url,{data:idata},function(data){
		if(data.trim()=='true'){
				$("#could_deliver").html("有货");
				$("#could_buy_button").show();
 				$("#could_not_buy_button").hide();
 		}else{
				$("#could_deliver").html("无货");
				$("#could_buy_button").hide();
  				$("#could_not_buy_button").show();
 		}
 	},'text');  
}
 </script>
   <?php include('/widget/footer.php'); ?>
</body>
</html>
<?php
mysql_free_result($consignee);

add_view_history($colname_product);
?>
