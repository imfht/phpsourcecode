<?php
/*
 * 权限分配类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Auth extends Action {

	private $cacheDir = ''; //缓存目录

	public function __construct() {
		$this->check_login();
		$this->authorization();
		@define( 'SYS_USER_ACCOUNT', $_SESSION["CRM"]["USER"]["account"] ); //定义
		@define( 'SYS_USER_ID', $_SESSION["CRM"]["USER"]["userID" ] ); //定义
		@define( 'SYS_USER_VIEW', $_SESSION["CRM"]["USER"]["viewID" ] ); //定义查看的权限		
	}
	//检查是否有登录
	public function check_login() {
		if ( empty( $_SESSION["CRM"]["USER" ]["account"] ) || empty( $_SESSION["CRM"]["USER"]["userID"] ) ) {
			//if ($_SESSION["CRM"]["USER"]['ischeck'] != 1 ) {
				//$_SESSION["CRM"]["USER"]['ischeck'] = 1;
				$this->location( "", '/Login/login', 0 );
			//}
		}
	}

	//判断是有执行方法的权限
	public function authorization() {
		if ( isset( $_SESSION[ "CRM" ][ "NEED" ][ "method" ] ) ) {
			if ( in_array( METHOD_NAME, $_SESSION[ "CRM" ][ "NEED" ][ "method" ] ) ) {
				if ( !in_array( METHOD_NAME, $_SESSION[ "CRM" ][ "USER" ][ "method" ] ) ) {
					$this->L( "Common" )->ajax_alert( "您无权限进行当前的操作！如果需要使用请联系管理员~", "info", "close" );
				}
			}
		}
	}
	
	//得需要验证的栏目和方法
	//返回：array("1",3,5,5) array('add',modify,del...);
	public function auth_menu_tree_arr() {
		$menu = $_SESSION[ "CRM" ][ "USER" ][ "menustr" ];
		if ( !empty( $menu ) ) {
			$sql = "select * from fly_sys_menu where visible='1' and id in ($menu)  order by sort asc,id desc;";
			$list = $this->C( $this->cacheDir )->findAll( $sql );
			$data = _instance( 'Extend/Tree' )->arrToTree( $list, 0 );
			return $data;
		} else {
			$this->location( "", '/Login/login', 0 );
		}
	}

	//界面初始化操作
	public function sys_default_conf() {
		return array(
			'title' => '07FLY-CRM客户关系管理系统',
			'companyname' => '成都零起飞网络工作室',
			'adtitle' => '您的需求我们来实现，用我们的真诚打造您的品牌。 ',
			'principal' => '开发人生',
			'tel' => '18030402705 ',
			'address' => '成都市校园路东368号 ',
			'email' => 'goodmuzi@qq.com ',
			'login_logo' => '',
			'manage_logo' => '',
			'login_title' => '07FLY-CRM',
			'copyright' => 'Copyright © 2017 - 07FLY-CRM ',
			'i_title' => '07FLY-CRM客户关系管理系统 ',
			'i_weibo' => '官方微博:http://weibo.com/u/2299441430 ',
			'i_note' => '<script type="text/javascript" src="http://bbs.07fly.net"></script>',
			'i_web' => 'http://www.07fly.top/a/crm',
			'i_copy' => '
<div class="divider"></div>
<h2>有限担保和免责声明:</h2>
<pre style="margin:5px;line-height:1.6em">
本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。
用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，
我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。
究相关责任的权力。
</pre>',
			'i_ser' => '
<div class="divider"></div>
<h2>有偿服务请联系:</h2>
<pre style="margin:5px;line-height:1.6em;">
<font color="#FF0000">定制化开发,公司培训,技术支持,解决使用过程中出现的全部疑难问题</font>
开发团队：零起飞网络
合作电话：18030402705(李先生)
技术支持：goodmuzi@qq.com
</pre>'
		);
	}

}//end class
?>