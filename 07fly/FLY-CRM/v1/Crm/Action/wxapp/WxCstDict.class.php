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
class WxCstDict extends Action {

	private $cacheDir  =''; //缓存目录
	private $pcCstDict	='';
	public function __construct() {
		_instance('Action/Auth');
		$this->pcCstDict=_instance('Action/CstDict');
	}
	public function cst_dict_opt($type,$optname,$optvalue=null){
		$sql	  ="select id,name from cst_dict where type='$type' order by sort asc;";
		$list	  =$this->C($this->cacheDir)->findAll($sql);
		$opthtml  ="<select name='$type'>";
		foreach($list as $row){
        $opthtml .="<option value='".$row['id']."'>".$row['name']."</option>";
		}
		$opthtml .='</select>';
		return $opthtml;
	}	
	
	
}//end class
?>