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
 
	//返回用户是否已经评论过这个商品
 
function user_could_comment($user_id, $product_id) {
	 
 
//	检查用户评论过得这个商品的数目，如果这个数目是0的话，那么直接返回true
 	$query_order = "SELECT orders.id, orders.user_id,order_item.order_id,order_item.product_id  FROM orders LEFT JOIN order_item ON orders.id=order_item.id WHERE orders.`user_id`=$user_id AND   order_item.product_id=$product_id";
	$order = mysql_query ( $query_order ) or die ( mysql_error () );
	$totalRows_order = mysql_num_rows ( $order );
	
 	if ($totalRows_order == 0) {
		return false;
	}
	
	$product_comment_num_sql="select count(*) as comment_num from product_comment where product_id=$product_id and user_id=$user_id";
	$product_comment_num_query = mysql_query ( $product_comment_num_sql ) or die ( mysql_error () );
	$product_comment_num=mysql_fetch_assoc($product_comment_num_query);
	//	 检查用户购买这个商品的数目，如果这个数目是0的话，那么直接返回false
	if((int)$product_comment_num['comment_num']==0){
 		return true;
	}
 	 
// 检查2个数目，如果购买的数目>评论的数目的话，那么可以直接返回tru不能评论了
	return $totalRows_order>(int)$product_comment_num['comment_num'];
}

function add_view_history($product_id) {
  	
	_add_session_view_history($product_id);
	
	_add_db_view_history($product_id);
 }

function _add_session_view_history($product_id){
	
	//	检查浏览记录是否设置了，如果设置过了的话，那么将其设置为空
	if (isset ( $_SESSION ['view_history'] ) || !is_array ( $_SESSION ['view_history'] )) {
		$_SESSION ['view_history'] = array ();
	}
	
	$create_time=date('Y-m-d H:i:s');
 	//$item['user_id']=$create_time;
	$item['product_id']=$product_id;
	$item['creat_time']=$create_time;
 	$_SESSION ['view_history'][]=$item;
}

function _add_db_view_history($product_id){
	//	检查里面是否已经存在了这个产品，如果有的话，那么删除这个产品，然后
	if(isset($_SESSION['user_id'])){
		$sql=sprintf("insert into user_view_history (user_id,product_id) values('%s','%s')",$_SESSION['user_id'],$product_id);
		mysql_query($sql) or die(mysql_error());
	}
}

 

/**
	检查是否在运送范围之内
**/
function could_devliver($areas){
		
		if(!is_array($areas)){
			return false;
		}
		
		$query_area = "SELECT * from shipping_method_area where is_delete=0";
		$area = mysql_query ( $query_area ) or die ( mysql_error () );
		while($order_area=mysql_fetch_assoc($area)){
			foreach($areas as $area_item){
				if(strpos($order_area['area'],$area_item)>-1){
  					return true;
				}	
			}
		}
		return false;
}
