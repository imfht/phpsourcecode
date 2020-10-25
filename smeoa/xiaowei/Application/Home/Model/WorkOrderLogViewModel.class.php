<?php
/*---------------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             


  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei               
 -------------------------------------------------------------------------*/

namespace Home\Model;
use Think\Model\ViewModel;

class  WorkOrderLogViewModel extends ViewModel {
	public $viewFields=array(
		'WorkOrderLog'=>array('*'),
		'WorkOrder'=>array('name','content','request_arrive_time','order_type','is_del','status','_on'=>'WorkOrderLog.task_id=WorkOrder.id')
		);
}
?>