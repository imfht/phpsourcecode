<?php
/*---------------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             


  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model\ViewModel;

class  PushViewModel extends ViewModel {
	public $viewFields=array(
		'Push'=>array('*'),
		'User'=>array('name','openid','_on'=>'Push.user_id=User.id')
		);
}
?>