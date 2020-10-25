<?php

namespace Muushop\Widget;
use Think\Controller;

/**
 * 支付驱动选择列表widget
 */

class PaychannelWidget extends Controller{
	
	public function lists(){
		$payChannel = D('Muushop/MuushopPay')->channel();
		if(empty($payChannel)){
			echo "支付驱动调用错误！";
		}
        $this->assign('payChannel',$payChannel);
        $this->display('Widget/paychannel');
	}
}