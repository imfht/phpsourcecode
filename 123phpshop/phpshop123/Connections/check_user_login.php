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
$user_admin_area_url = "/user/index.php";
$user_login_url = "/login.php";

//	检查当前是否属于用户控面板区域或是需要登录的话
if (_is_user_admin_area () ||  _is_need_login_page()) {
	//	检查用户是否已经登录，如果已经登录的话，那么直接跳转到用户的管理面板。
	if (_is_user_logged_in ()) {
		
		//		如果用户已经登录的话，而且当前页面是登录界面的话，那么直接跳转到用户的管理面板
		if (_is_user_login_page ()) {
			header ( sprintf ( "Location: %s", $user_admin_area_url ) );
		}
	} else {
		//	检查当前是否是用户登录界面，如果不是的话怎么跳转到用户的登录界面。
		if (! _is_user_login_page ()) {
			header ( sprintf ( "Location: %s", $user_login_url ) );
		}
	
	}

}


function _is_need_login_page(){
	$_need_login_pages=array('/pay.php','/payoff.php');
	$curr_url = $_SERVER ['REQUEST_URI'];
	return in_array($curr_url,$_need_login_pages);
 }

/**
 * 检查当前区域是否属于用户管理区。
 */
function _is_user_admin_area() {
	$curr_url = $_SERVER ['REQUEST_URI'];
	return strpos ( $curr_url, '/user/' )>-1;
}

/**
 * 检查有否是否已经登录？
 */
function _is_user_logged_in() {
	return isset ( $_SESSION ['user_id'] ) ;
}

/**
 * 等检查当前页面是否属于用户登录页面。
 */
function _is_user_login_page() {
	$curr_url = $_SERVER ['REQUEST_URI'];
	return $curr_url == '/login.php';
} 
