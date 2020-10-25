<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

defined('IS_IN') or die('Include Error!');

class EmailModel extends RcModel{
	
	/**
	 * 获取邮件发送模板内容
	 * @param $type		模板类型
	 * @param $info		给模板的变量
	 */
	public function getSendBody( $type, $info=false){
		rc::smarty()->assign('sendemail',$type);
		$nowTemplatesPath = rc::smarty()->template_dir;
		rc::smarty()->assign('info', $info);
		$body = rc::smarty()->fetch('EmailAction/index-email.html');
		rc::smarty()->template_dir = $nowTemplatesPath;
		return $body;
	}
}