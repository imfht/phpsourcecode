<?php
/*---------------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             


  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model\ViewModel;

class  FinanceViewModel extends ViewModel {
	public $viewFields=array(
		'Finance'=>array('*'),
		'FinanceAccount'=>array('name'=>'account_name','_on'=>'Finance.account_id=FinanceAccount.id')
		);
}
?>