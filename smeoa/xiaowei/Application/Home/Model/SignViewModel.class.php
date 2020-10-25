<?php
/*---------------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             

  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model\ViewModel;

class  SignViewModel extends ViewModel {
	public $viewFields=array(
		'User'=>array('id'=>'user_id','emp_no','name','letter','dept_id','position_id','email','duty','office_tel','mobile_tel','pic','birthday','sex','password','is_del'),
		'Position'=>array('name'=>'position_name','_on'=>'Position.id=User.position_id'),
		'Sign'=>array('id','sign_date','latitude','longitude','type','is_real_time','create_time','location','content','ip','_on'=>'Sign.user_id=User.id'),
		'Dept'=>array('name'=>'dept_name','_on'=>'Dept.id=User.dept_id')
		);
}
?>