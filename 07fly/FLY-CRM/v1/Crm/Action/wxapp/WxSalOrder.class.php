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
class WxSalOrder extends Action {

	private $cacheDir  =''; //缓存目录
	private $pcSalOrder ='';
	public function __construct() {
		_instance('Action/Auth');
		$this->pcSalOrder=_instance('Action/SalOrder');
	}
	public function sal_order(){
		$assArr = $this->pcSalOrder->sal_order();
		echo json_encode($assArr);
	}
	public function sal_order_show(){
		$assArr = $this->pcSalOrder->sal_order();
		$smarty = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('wxapp/sal_order_show.html');	
	}	
		
}//end class
?>