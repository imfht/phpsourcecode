<?php
final class Common {
	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
	}
	

	public function genOrderSN()
	{
	  	mt_srand((double) microtime() * 1000000);
	    return date('Ymdh') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	}
	
	// used to format order id for display and reget true order id for php
	public function setOrderId($order_id,$order_add_date='') {
		if($order_add_date!='')
			$order_add_date=time();
		$str=date('Ymdh',strtotime($order_add_date)).strval($order_id);
		return $str;
	}
  	
	public function getOrderId($order_id_display) {
		$str=(int)substr($order_id_display,10,strlen($order_id_display));	

  	}
  	
  	public function getCustomerName($customer_id) {
  		$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id= '" . $customer_id. "'");
  		if ($customer_query->num_rows) {
  			return  $customer_query->row['username'];
  		}else{
  			return  '';
  		}
  	}
  	
}
?>