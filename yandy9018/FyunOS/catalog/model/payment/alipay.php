<?php 
class ModelPaymentAlipay extends Model {
  	public function getMethod($address) {
		$this->load->language('payment/alipay');
		
		if ($this->config->get('alipay_status')) {
      		$status = TRUE;
      	} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         => 'alipay',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('alipay_sort_order')
      		);
    	}
	
    	return $method_data;
  	}
}
?>