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
class WxSalContract extends Action {

	private $cacheDir =''; //缓存目录
	private $pcSalContract ='';
	public function __construct() {
		_instance('Action/Auth');
		$this->pcSalContract=_instance('Action/SalContract');
	}
	public function sal_contract(){
		$assArr = $this->pcSalContract->sal_contract();
		echo json_encode($assArr);
	}
	public function sal_contract_show(){
			$assArr  = $this->pcSalContract->sal_contract();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('wxapp/sal_contract_show.html');	
	}		
}//end class
?>