<?php
/*---------------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             


  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
 -------------------------------------------------------------------------*/


namespace Home\Model;
use Think\Model;

class  MailModel extends CommonModel {
	// 自动验证设置
	protected $_validate = array( array('name', 'require', '标题必须！', 1), array('content', 'require', '内容必须'), );
}
?>