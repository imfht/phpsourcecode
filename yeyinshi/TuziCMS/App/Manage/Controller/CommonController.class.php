<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/

namespace Manage\Controller;
use Think\Controller;
class CommonController extends Controller {
	/**
	 * 公共验证控制器CommonAction
	 */
	Public function _initialize(){
		header("Content-Type:text/html; charset=utf-8");
		session_start();
		if (!isset($_SESSION['admin_name']) || $_SESSION['admin_name']==''){
			$this->error('请勿无权访问！你的ip已被记录',U('Login/index'));	
		}
	}
}