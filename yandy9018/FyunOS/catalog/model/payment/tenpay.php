<?php 
class ModelPaymentTenpay extends Model {
  	public function getMethod($address) {
		$this->load->language('payment/tenpay');
		
		if ($this->config->get('tenpay_status')) {
      		$status = TRUE;
      	} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         => 'tenpay',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('tenpay_sort_order')
      		);
    	}
	
    	return $method_data;
  	}
}
?>