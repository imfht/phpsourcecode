<?php
/*
 * 小程序api类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class WxCstTrace extends Action {

	private $cacheDir  =''; //缓存目录
	private $pcCstTrace ='';
	public function __construct() {
		_instance('Action/Auth');
		$this->pcCstTrace=_instance('Action/CstTrace');
	}
	public function cst_trace(){
		$assArr = $this->pcCstTrace->cst_trace();
		echo json_encode($assArr);
	}
	public function cst_trace_show(){
		$assArr = $this->pcCstTrace->cst_trace();
		$smarty = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('wxapp/cst_trace_show.html');	
	}		
}//end class
?>