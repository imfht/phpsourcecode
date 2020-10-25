<?php
/*---------------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             


  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model\ViewModel;

class  WorkLogViewModel extends ViewModel {
	public $viewFields=array(
		'WorkLog'=>array('*'),
		'User'=>array('emp_no','name'=>'user_name','letter','dept_id','position_id','email','duty','office_tel','mobile_tel','pic','birthday','sex','_on'=>'WorkLog.user_id=User.id'),
		'Position'=>array('name'=>'position_name','_on'=>'Position.id=User.position_id'),
		'Dept'=>array('name'=>'dept_name','_on'=>'Dept.id=User.dept_id')
		);
}
?>