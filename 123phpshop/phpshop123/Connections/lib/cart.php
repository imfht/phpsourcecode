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
class Cart {
	
	/**
	 * 构造函数
	 */
	public function __construct() {
 	
		//		这里检查session是否开启，如果没有开启，那么开启
		if(!$this->_is_cart_initialized()){
			$this->_init_cart ();
			$this->clear();
		}
 	}
	
	/**
	 * 将产品添加到购物车
	 * Enter description here ...
	 * @param unknown_type $product
	 */
	public function add($product) {
		// 获取默认收货地址
		
		// 如果用户已经登录的话，那么获取用户的默认收货地址
		
		// 如果用户没有登录的话，那么获取系统的默认收货区域
		
		// 检查商品是否可以配送到这个区域，如果不能配送到这个区域，那么返回false
		
		
 		//	如果session中的产品的数量为0的话，那么直接将产品添加到购物车中的产品列表中即可
		$_is_product_exits_in_cart = $this->_is_product_exits_in_cart ( $product );
		if (! $_is_product_exits_in_cart) {
			//		如果不为0 的话，那么需要检查购物车中是否有这个产品，如果有的话，那么更新这个产品的数量
			$this->_do_add_product ( $product );
		} else {
			// 如果没有这个产品的话，那么将这个产品更新到session中的产品中
			$this->_update_product_quantity ( $product );
		}
		
		//		更新产品总价
		$this->_update_products_total ();
		
		// 更新运费
		$this->_update_shipping_fee ();
		
		//		更新订单总价
		$this->_update_order_total ();
	
	}
	
	/**
	 * 减少购物车中某产品的数量
	 * @param unknown_type $product_id
	 * @param unknown_type $quantity
	 */
	public function decrease_quantity($product_id, $quantity) {
		//		检查产品是否存在，如果不存在，那么告知重新刷新页面
		$product = $this->_get_product_by_id ( $product_id );
		if (! $product) {
			throw new Exception ( "产品不存在，请刷新页面后重试" );
		}
		//		如果存在，那么检查产品的数量是否为1，如果为1的话，那么告知不能减低
		if (( int ) $product ['quantity'] == 1) {
			throw new Exception ( "请至少保留一件此商品，如果需要删除此件商品的话，请点击删除链接" );
		}
		//		如果都ok的话，那么这个产品的数量-1，然后返回更新后的数据
		if (!$this->_do_decrease_quantity ( $product_id, $quantity )) {
			throw new Exception ( "系统错误，请稍后重试" );
		}
		//		更新产品总价
		$this->_update_products_total ();
		
		// 更新运费
		$this->_update_shipping_fee ();
		
		//		更新订单总价
		$this->_update_order_total ();
		
		return true;
	}
	
	/**
	 * 增加购物车中某产品的数量
	 * @param unknown_type $product_id
	 * @param unknown_type $quantity
	 */
	public function change_quantity($product_id, $quantity,$attr_value) {
		
		//		检查产品是否存在，如果不存在，那么告知重新刷新页面
		$product = $this->_get_product_by_id_attr_value ( $product_id ,$attr_value);
		if (! $product) {
			throw new Exception ( "产品不存在，请刷新页面后重试" );
		}
		
		//		如果都ok的话，那么这个产品的数量+1，然后返回更新后的数据
		if (!$this->_do_change_quantity ( $product_id, $quantity,$attr_value )) {
			throw new Exception ( "系统错误，请稍后重试" );
		}
		
		//		更新产品总价
		$this->_update_products_total ();
		
		// 更新运费
		$this->_update_shipping_fee ();
		
		//		更新订单总价
		$this->_update_order_total ();
		
		return true;
	}
	
	private function _do_change_quantity($product_id, $quantity,$attr_value) {
		
		//		如果没有设置过产品的session信息，或者是设置过产品的session信息但是里面没有产品的话，那么直接返回false
		if (! isset ( $_SESSION ['cart'] ['products'] ) || empty ( $_SESSION ['cart'] ['products'] )) {
			return false;
		}
		
		//		循环里面的每一个产品
		for($i = 0; $i < count ( $_SESSION ['cart'] ['products'] ); $i ++) {
			if (( int ) $_SESSION ['cart'] ['products'] [$i] ['product_id'] == ( int ) $product_id && $_SESSION ['cart'] ['products'] [$i] ['attr_value'] == $attr_value) {
				$_SESSION ['cart'] ['products'] [$i] ['quantity'] =$quantity;
				return true;
			}
		}
		
		return false;
	
	}
	
	
	private function _is_cart_initialized(){
	
 		if(isset($_SESSION['cart']['products']) 
		&& isset($_SESSION['cart']['products_total']) 
		&& isset($_SESSION['cart']['shipping_fee'])
		&& isset($_SESSION['cart']['order_total'])){
			return true;
		}
		return false;
	}
	
	/**
	 * 检查产品是否在购物车里面
	 * @param unknown_type $product
	 */
	private function _is_product_exits_in_cart($product) {
		
		//isset($product['Submit'])?unset($product['Submit']):'';
 
		//		如果没有设置过产品的session信息，或者是设置过产品的session信息但是里面没有产品的话，那么直接返回false
		if (! isset ( $_SESSION ['cart'] ['products'] ) || empty ( $_SESSION ['cart'] ['products'] )) {
			return false;
		}
		
		//		循环里面的每一个产品
		foreach ( $_SESSION ['cart'] ['products'] as $item ) {
			
			
			if (! isset ( $item ['product_id'] ) || ! isset ( $product ['product_id'] )) {
				
				continue;
			}
			if (( int ) $item ['product_id'] == ( int ) $product ['product_id'] && $item ['attr_value']==  $product ['attr_value'] ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * 检查产品是否在购物车里面
	 * @param unknown_type $product
	 */
	private function _is_product_exits_in_cart_by_id($product_id) {
		
		//		如果没有设置过产品的session信息，或者是设置过产品的session信息但是里面没有产品的话，那么直接返回false
		if (! isset ( $_SESSION ['cart'] ['products'] ) || empty ( $_SESSION ['cart'] ['products'] )) {
			return false;
		}
		
		//		循环里面的每一个产品
		foreach ( $_SESSION ['cart'] ['products'] as $item ) {
			if ($item ['product_id'] == $product_id) {
				return true;
			}
		}
		return false;
	}
	
	private function _is_product_exits_in_cart_by_id_attr_value($product_id,$attr_value) {
		
		//		如果没有设置过产品的session信息，或者是设置过产品的session信息但是里面没有产品的话，那么直接返回false
		if (! isset ( $_SESSION ['cart'] ['products'] ) || empty ( $_SESSION ['cart'] ['products'] )) {
			return false;
		}
		
		//		循环里面的每一个产品
		foreach ( $_SESSION ['cart'] ['products'] as $item ) {
			if ($item ['product_id'] == $product_id && $item ['attr_value'] == $attr_value) {
				return true;
			}
		}
		return false;
	}
	
	private function _get_product_by_id($product_id) {
		//		如果没有设置过产品的session信息，或者是设置过产品的session信息但是里面没有产品的话，那么直接返回false
		if (! isset ( $_SESSION ['cart'] ['products'] ) || empty ( $_SESSION ['cart'] ['products'] )) {
			return false;
		}
		
		//		循环里面的每一个产品
		foreach ( $_SESSION ['cart'] ['products'] as $item ) {
			if ($item ['product_id'] == $product_id) {
				return $item;
			}
		}
		return false;
	}
	
	private function _get_product_by_id_attr_value($product_id,$attr_value) {
		//		如果没有设置过产品的session信息，或者是设置过产品的session信息但是里面没有产品的话，那么直接返回false
		if (! isset ( $_SESSION ['cart'] ['products'] ) || empty ( $_SESSION ['cart'] ['products'] )) {
			return false;
		}
		
		//		循环里面的每一个产品
		foreach ( $_SESSION ['cart'] ['products'] as $item ) {
			if ($item ['product_id'] == $product_id && $item ['attr_value'] == $attr_value) {
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * 正式添加
	 * Enter description here ...
	 * @param unknown_type $product
	 */
	private function _do_add_product($product) {
 //			这里需要根据product的id获取相应的产品的价格
		$price=$this->_get_product_price_from_db_by_id($product ['product_id']);
		$product['product_price']=$price;
 		$_SESSION ['cart'] ['products'] [] = $product;
	}
	
	// 从数据库里面获取产品的价格
	private function _get_product_price_from_db_by_id($product_id){
	
//			这里还是需要获取是否有优惠价格
		require_once ($_SERVER['DOCUMENT_ROOT'].'/Connections/localhost.php');

		mysql_select_db($database_localhost);
		$query_product = "SELECT id, price FROM product WHERE id = ".$product_id;
		$product = mysql_query($query_product) or die(mysql_error());
		$row_product = mysql_fetch_assoc($product);
		//$totalRows_product = mysql_num_rows($product);
		return $row_product['price'];
	}
	
	/**
	 * 更新购物车里面这个产品的数量
	 * @param unknown_type $product
	 */
	private function _update_product_quantity($product) {
		
		// 检查session中是否有产品，如果没有的话，那么直接返回false
		if (! isset ( $_SESSION ['cart'] ['products'] ) || count ( $_SESSION ['cart'] ['products'] ) == 0) {
			return false;
		}
		
		
		// 如果session中有产品，那么循环这些产品
		for($i = 0; $i < count ( $_SESSION ['cart'] ['products'] ); $i ++) {
			
			// 如果这个产品没有产品id的属性
			if (! isset ( $_SESSION ['cart'] ['products'] [$i] ['product_id'] )) {
				continue;
			}
			
			// 如果这个产品的id和循环中的产品的id相同
			if ($_SESSION ['cart'] ['products'] [$i] ['product_id'] == $product ['product_id'] && $_SESSION ['cart'] ['products'] [$i] ['attr_value'] == $product ['attr_value']) {
				$_SESSION ['cart'] ['products'] [$i] ['quantity'] = ( int ) $_SESSION ['cart'] ['products'] [$i] ['quantity'] + ( int ) $product ['quantity'];
				return true;
			}
		}
		
//			如果找不到这个产品的话，那么直接返回false
		return false;
	}
	
	/**
	 * 删除这个产品
	 * @param unknown_type $product
	 */
	public function remove($product_id,$attr_value) {
		//		检查这个产品是否在cart中，如果在的话，那么将这个产品从购物车中移除
		if (! $this->_is_product_exits_in_cart_by_id_attr_value ( $product_id ,$attr_value)) {
 			return true;
		}
		
		//		如果不在的话，那么直接返回true
		$this->_do_remove_from_cart ( $product_id,$attr_value );
		
		//		更新产品总价
		$this->_update_products_total ();
		
		// 更新运费
		$this->_update_shipping_fee ();
		
		//		更新订单总价
		$this->_update_order_total ();
		return true;
	}
	/**
	 * 从购物车中删除某个产品。
	 * @param unknown_type $product_id
	 */
	private function _do_remove_from_cart($product_id,$attr_value) {
		//		循环购物车中的所有产品，然后检查他们的产品id，如果当前的产品id和我们所需要的产品id是一致的话删除。
		for($i = 0; $i < count ( $_SESSION ['cart'] ['products'] ); $i ++) {
			if ((int)$_SESSION ['cart'] ['products'] [$i] ['product_id'] == (int)$product_id &&$_SESSION ['cart'] ['products'] [$i] ['attr_value']== $attr_value) {
				unset ( $_SESSION ['cart'] ['products'] [$i]);
				break;
			}
		}
		sort ( $_SESSION ['cart'] ['products'] );
		return true;
	}
	
	/**
	 * 获取购物车数据
	 * Enter description here ...
	 */
	public function get() {
		
		if (! isset ( $_SESSION ['cart'] )) {
			$this->_init_cart ();
		}
		return $_SESSION ['cart'];
	}
	
	/**
	 * 更新购物车里面的产品
	 * Enter description here ...
	 */
	public function update() {
		
	}
	
	/**
	 * 清除购物车中的所有产品
	 * Enter description here ...
	 */
	public function clear() {
		$this->_init_cart ();
	}
	
	/**
	 * 初始化购物车
	 */
	private function _init_cart() {
		
		//		检查session是否开启，如果没有开启的话，那么开启session；
		if (! isset ( $_SESSION )) {
			    session_start ();
				
		}
		
		//		如果开启的话，那么检查是否已经初始化了cart，如果没有的话 ，那么进行初始化
		$_SESSION ['cart'] ['products'] =  array ();
		$_SESSION ['cart'] ['products_total'] =0.00;
		$_SESSION ['cart'] ['shipping_fee'] = 0.00;
		$_SESSION ['cart'] ['order_total'] = 0.00;
			
 	}
	
	//		更新产品总价
	private function _update_products_total() {
		$product_total = 0;
		if (empty ( $_SESSION ['cart'] ['products'] )) {
			return $product_total;
		}
		
		//		对订单中的每个产品的总价进行累加
		foreach ( $_SESSION ['cart'] ['products'] as $product ) {
			if (! isset ( $product ['product_price'] )) {
				continue;
			}
			$product_total += floatval ( $product ['product_price'] ) * $product ['quantity'];
		}
		
		$_SESSION ['cart'] ['products_total'] = $product_total;
	}
	// 更新运费
	private function _update_shipping_fee() {
		require_once($_SERVER['DOCUMENT_ROOT']."/Connections/lib/order.php");
		$shipping_fee=get_shipping_fee();
		$_SESSION ['cart'] ['shipping_fee'] = $shipping_fee['shipping_fee'];
		$_SESSION ['cart'] ['shipping_method_id'] = $shipping_fee['shipping_fee_plan'];
		return true;
	}
	
	// 更新运费
	private function update_shipping_fee() {
		require_once($_SERVER['DOCUMENT_ROOT']."/Connections/lib/order.php");
		$shipping_fee=get_shipping_fee();
		$_SESSION ['cart'] ['shipping_fee'] = $shipping_fee['shipping_fee'];
		$_SESSION ['cart'] ['shipping_method_id'] = $shipping_fee['shipping_fee_plan'];
		return true;
	}
	
	
	//		更新订单总价
	private function _update_order_total() {
		return $_SESSION ['cart'] ['order_total'] = floatval ( $_SESSION ['cart'] ['shipping_fee'] ) + floatval ( $_SESSION ['cart'] ['products_total'] );
		 
	}
}