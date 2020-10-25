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

function get_sort_link($para){
	switch($para){
	
		case 1:
			return get_sales_link();
			break;
		case 2:
			return get_price_link();
		break;
		
		case 3:
			return get_comments_link();
		break;
		case 4:
			return get_sheft_link();
		break;
		
		default:
			return "";
	}
}

function get_sales_link(){
	$result="";
	$sort="asc";
	if(!isset($_GET['keywords'])){return $result;}
	$base_url="/search.php?keywords=".$_GET['keywords']."&sort_by=sales&sort=";
	if(isset($_GET['sort']) && $_GET['sort']=="asc"){
		return $base_url.="desc";
	}
	return $base_url.=$sort;
}

function get_price_link(){
	$result="";
	$sort="asc";
	if(!isset($_GET['keywords'])){return $result;}
	$base_url="/search.php?keywords=".$_GET['keywords']."&sort_by=price&sort=";
	if(isset($_GET['sort']) && $_GET['sort']=="asc"){
		return $base_url.="desc";
	}
	return $base_url.=$sort;
}

function get_comments_link(){
	$result="";
	$sort="asc";
	if(!isset($_GET['keywords'])){return $result;}
	$base_url="/search.php?keywords=".$_GET['keywords']."&sort_by=comments&sort=";
	if(isset($_GET['sort']) && $_GET['sort']=="asc"){
		return $base_url.="desc";
	}
	return $base_url.=$sort;
}

function get_sheft_link(){
	$result="";
	$sort="asc";
	if(!isset($_GET['keywords'])){return $result;}
	$base_url="/search.php?keywords=".$_GET['keywords']."&sort_by=sheft&sort=";
	if(isset($_GET['sort']) && $_GET['sort']=="asc"){
		return $base_url.="desc";
	}
	return $base_url.=$sort;
}

function _get_order_by(){
	 $result='';
	if(isset($_GET['sort_by']) && trim(isset($_GET['sort_by']))!='' && trim($_GET['sort_by'])=='price' && isset($_GET['sort']) && trim(isset($_GET['sort']))!=''){
 		$result=" order by  price ".$_GET['sort'];
 	}
 	
if(isset($_GET['sort_by']) && trim(isset($_GET['sort_by']))!='' && trim($_GET['sort_by'])=='comments' && isset($_GET['sort']) && trim(isset($_GET['sort']))!=''){
 		$result=" order by  commented_num ".$_GET['sort'];
 	}
 	
	if(isset($_GET['sort_by']) && trim(isset($_GET['sort_by']))!='' && trim($_GET['sort_by'])=='sheft' && isset($_GET['sort']) && trim(isset($_GET['sort']))!=''){
 		$result=" order by  on_sheft_time ".$_GET['sort'];
 	}
 	
	if(isset($_GET['sort_by']) && trim(isset($_GET['sort_by']))!='' && trim($_GET['sort_by'])=='sales' && isset($_GET['sort']) && trim(isset($_GET['sort']))!=''){
 		$result=" order by  sold_num ".$_GET['sort'];
 	}
	
	return $result;
}
?>

<table style="margin-left:7px;" width="216" height="25" border="1" cellspacing="0" bordercolor="#CCC" bgcolor="#FFF">
          <tr align="center">
            <td height="25" <?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='sales'){ ?>class="sort_box_activated"　<?php }?>><a href="<?php echo get_sort_link(1);?>"><span class="sort_box">销量<?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='sales' && $_GET['sort']=='asc'){ ?>↑<?php }?><?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='sales' && $_GET['sort']=='desc'){ ?>↓<?php }?></span></a></td>
            <td height="25" <?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='price'){ ?>class="sort_box_activated"　<?php }?>><a href="<?php echo get_sort_link(2);?>"><span class="sort_box">价格<?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='price' && $_GET['sort']=='asc'){ ?>↑<?php }?><?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='price' && $_GET['sort']=='desc'){ ?>↓<?php }?></span></a></td>
            <td height="25" <?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='comments'){ ?>class="sort_box_activated"　<?php }?>><a href="<?php echo get_sort_link(3);?>"><span class="sort_box">评论数<?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='comments' && $_GET['sort']=='asc'){ ?>↑<?php }?><?php if(isset($_GET['sort_by']) &&$_GET['sort_by']=='comments' && $_GET['sort']=='desc'){ ?>↓<?php }?></span></a></td>
            <td height="25" <?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='sheft'){ ?>class="sort_box_activated"　<?php }?>><a href="<?php echo get_sort_link(4);?>"><span class="sort_box">上架时间<?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='sheft' && $_GET['sort']=='asc'){ ?>↑<?php }?><?php if(isset($_GET['sort_by']) && $_GET['sort_by']=='sheft' && $_GET['sort']=='desc'){ ?>↓<?php }?></span></a></td>
          </tr>	
        </table>