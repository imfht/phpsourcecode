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
$admin_login_url = '/admin/login.php';
$admin_index_url = '/admin/index.php';

//	检查当前是不属于管理员区域。如果是的话那么检查用户是否已经登录。
if (_is_admin_area ()) {
	
	$_is_admin_login_page 	= _is_admin_login_page ();
	$_is_admin 				= _is_admin ();
	
	//	那么检查用户是否属于管理员？如果用户不属于的话，那么跳转到登录页面。
	if (! $_is_admin ) {
		if(! $_is_admin_login_page){
			header ( sprintf ( "Location: %s", $admin_login_url ) );
		}
	} else {
		//	如果用户是管理员的话,检查当前页面是否是登录页面，如果是登录页面的话，怎么直接跳到管理员主界面即可。
		if ($_is_admin_login_page) {
			header ( sprintf ( "Location: %s", $admin_index_url ) );
		}
	}
}

/**
 * 检查当前页面是否属于管理员页面。
 */
function _is_admin_area() {
	$curr_url = $_SERVER ['REQUEST_URI'];
	return strpos ( $curr_url, '/admin/' ) > - 1;
}

/**
 * 检查当前用户是否属于管理员角色。
 */
function _is_admin() {
	return isset ( $_SESSION ['admin_id'] );
}

/**
 * 检查当前页面是否属于管理员登录页面。
 */
function _is_admin_login_page() {
	$curr_url = $_SERVER ['REQUEST_URI'];
	return strpos ( $curr_url, '/admin/login.php' ) > - 1;
}