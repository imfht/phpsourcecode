<?php
class paymenterApi extends baseApi{
	
	public function getMenu(){
		return array(
					'sort'=>1,
					'title'=>'支付设置',
					'url'=>url('paymenter/index/paymenterlist'),
			);
	}
	
}