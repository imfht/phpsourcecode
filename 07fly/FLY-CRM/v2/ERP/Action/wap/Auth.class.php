<?php
/*
 * 验证类
 */	 
class Auth extends Action {

	private $cacheDir = ''; //缓存目录

	public function __construct() {
		$this->check_login();
		$this->authorization();
		//@define( 'SYS_USER_ACCOUNT', $_SESSION[ "CRM" ][ "USER" ][ "account" ] ); //定义
		@define( 'SYS_USER_ACCOUNT',$_SESSION["sys_user_acc"]); //定义
		//@define( 'SYS_USER_ID', $_SESSION[ "CRM" ][ "USER" ][ "userID" ] ); //定义
		define( 'SYS_USER_ID','1'); //定义
		@define( 'SYS_USER_VIEW','1,4' ); //定义查看的权限
		@define( 'SYS_CO_ID', '1' ); //定义所属于公司编号
	}

	//判断是有执行方法的权限
	public function authorization() {
		//print_r($_SESSION[ "CRM" ][ "USER" ][ "method" ]);
		if ( isset( $_SESSION[ "CRM" ][ "NEED" ][ "method" ] ) ) {
			if ( in_array( METHOD_NAME, $_SESSION[ "CRM" ][ "NEED" ][ "method" ] ) ) {
				if ( !in_array( METHOD_NAME, $_SESSION[ "CRM" ][ "USER" ][ "method" ] ) ) {
					$smarty = $this->setSmarty();
					$smarty->display( '404.html' );
					exit;
				}
			}
		}
	}

} //

?>