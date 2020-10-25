<?php
/**
 * 
 */
namespace app\people\controller;
use app\common\controller\Base;
use app\common\model\UserFocus;
class Api extends Base
{
/**
 * [recharge 支付处理]
 * @Author   Jerry
 * @DateTime 2017-06-11T21:14:46+0800
 * @Example  eg:
 * @return   [type]                   [description]
 */
  public function recharge(){
  	// show($_POST);
  	if($this->request->isPost()){
  		if((int)input('price')>0){
  			
  		}else{

			return returnJson(1,'','充值金额必须大于0');
  		}
  
  	}
  }

  

	
}