<?php
/*---------------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             


  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
 -------------------------------------------------------------------------*/


namespace Home\Model;
use Think\Model;

class  ContactModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('name','require','姓名必须！',1),
		array('email','email','邮箱格式错误！',2),
		);
}
?>