<?php
final class Customer {
	private $customer_id;
	private $firstname;
	private $lastname;
	//private $zone_id;
	//private $city_id;
	private $email;
	private $telephone;
	private $fax;
	private $newsletter;
	private $customer_group_id;
	private $address;
	private $payment_method;
	private $shipping_method;
	private $code;
	
  	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->url = $registry->get('url');
		$this->language = $registry->get('language');
		//$this->session->data['telephone']=13634548818;
		
		if(isset($this->session->data['open_id']) || isset($this->session->data['telephone'])){
		if (isset($this->session->data['open_id'])&&!isset($this->session->data['telephone'])) { 
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE open_id = '" .$this->session->data['open_id'] . "' AND status = '1'");
			}elseif(!isset($this->session->data['open_id'])&&isset($this->session->data['telephone'])){
				$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE telephone = '" .$this->session->data['telephone'] . "' AND status = '1'");
				}elseif(isset($this->session->data['open_id'])&&isset($this->session->data['telephone'])){
					$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE open_id = '" .$this->session->data['open_id'] . "' AND status = '1'");
					unset($this->session->data['telephone']);
					}
				
			if ($customer_query->num_rows) {
				//$this->session->data['customer_id'] = $customer_query->row['customer_id'];
				$this->customer_id = $customer_query->row['customer_id'];
				$this->firstname = $customer_query->row['firstname'];
				$this->telephone = $customer_query->row['telephone'];
				$this->phone_bind = $customer_query->row['phone_bind'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->zone_id = $customer_query->row['zone_id'];
				$this->city_id = $customer_query->row['city_id'];
				$this->address = $customer_query->row['address'];
				$this->payment_method = $customer_query->row['payment_method'];
				$this->shipping_method = $customer_query->row['shipping_method'];			
				$this->code = $customer_query->row['code'];
				
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
			
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->customer_id . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");
				
				if (!$query->num_rows) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$this->customer_id . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
				}
			
  		}
		
	}
	}
	
	public function weixinLogin() {
		if(isset($this->request->get['code'])){
				  $this->data['json_oauth2'] = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->config->get('config_wechat_appid')."&secret=".$this->config->get('config_wechat_appsecret')."&code=".$this->request->get['code']."&grant_type=authorization_code");
				  $this->data['oauth2'] = json_decode($this->data['json_oauth2'],true);
				  $this->session->data['open_id'] = $this->data['oauth2']['openid'];
				  $this->redirect($this->url->link('account/account', '', 'SSL'));
				}else{
					$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
					$this->redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->config->get('config_wechat_appid')."&redirect_uri=".$this->session->data['redirect']."&response_type=code&scope=snsapi_base&state=123#wechat_redirect");
					}
	}
	
	public function checkEmailExist($email) {
		$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer where LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND status = '1'");
		if ($customer_query->num_rows)
		return 1;
		else
		return 0;
	}
		
  	public function login($email, $password) {
  		if (!$this->config->get('config_customer_approval')) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1'");
		} else {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1' AND approved = '1'");
		}
		
		if ($customer_query->num_rows) {
			$this->session->data['customer_id'] = $customer_query->row['customer_id'];	
		    
			if (($customer_query->row['cart']) && (is_string($customer_query->row['cart']))) {
				$cart = unserialize($customer_query->row['cart']);
				
				foreach ($cart as $key => $value) {
					if (!array_key_exists($key, $this->session->data['cart'])) {
						$this->session->data['cart'][$key] = $value;
					} else {
						$this->session->data['cart'][$key] += $value;
					}
				}			
			}

			if (($customer_query->row['wishlist']) && (is_string($customer_query->row['wishlist']))) {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}
								
				$wishlist = unserialize($customer_query->row['wishlist']);
			
				foreach ($wishlist as $product_id) {
					if (!in_array($product_id, $this->session->data['wishlist'])) {
						$this->session->data['wishlist'][] = $product_id;
					}
				}			
			}
									
			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->phone_bind = $customer_query->row['phone_bind'];
			$this->fax = $customer_query->row['fax'];
			$this->newsletter = $customer_query->row['newsletter'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->address = $customer_query->row['address'];
           
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' ,  date_added = NOW() WHERE customer_id = '" . (int)$customer_query->row['customer_id'] . "'");
			
	  		return true;
    	} else {
      		return false;
    	}
  	}
  
    	public function mobileLogin($telephone) {
  		
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE telephone = '" . $this->db->escape($telephone) . "' AND status = '1'");
		
		
		if ($customer_query->num_rows) {
			if($customer_query->row['open_id']){
				$this->session->data['open_id'] = $customer_query->row['open_id'];
				unset($this->session->data['telephone']);
				}else{
					$this->session->data['telephone'] = $customer_query->row['telephone'];
					unset($this->session->data['open_id']);
					}					
			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->fax = $customer_query->row['fax'];
			$this->newsletter = $customer_query->row['newsletter'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->address = $customer_query->row['address'];
           
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' ,  date_added = NOW() WHERE customer_id = '" . (int)$customer_query->row['customer_id'] . "'");
			
	  		return true;
    	} else {
      		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET  firstname ='". $telephone . "',telephone ='". $telephone . "', status ='1',date_added =' NOW()', customer_group_id = '" . (int)$this->config->get('config_customer_group_id')."'");
			$this->mobileLogin($telephone);
    	}
  	}
  
    
	public function setAddress($address) {
  		$this->db->query("UPDATE " . DB_PREFIX . "customer SET address = '" . $this->db->escape($address) . "' WHERE customer_id = '" . (int)$this->getId() . "'");
  	}
  	
  	private function redirect($url, $status = 302) {
  		header('Status: ' . $status);
  		header('Location: ' . str_replace('&amp;', '&', $url));
  		exit();
  	}
  	
  	public function logout() {
		unset($this->session->data['open_id']);
		unset($this->session->data['telephone']);

		$this->customer_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->telephone = '';
		$this->fax = '';
		$this->newsletter = '';
		$this->customer_group_id = '';
		$this->address = '';
		
		session_destroy();
		$this->redirect($this->url->link('account/login', '', 'SSL'));
  	}
  
  	public function isLogged() {
    	return $this->customer_id;
  	}
	
	public function getPhoneBind() {
    	return $this->phone_bind;
  	}

  	public function getId() {
    	return $this->customer_id;
  	}
      
  	public function getFirstName() {
		return $this->firstname;
  	}
  
  	public function getLastName() {
		return $this->lastname;
  	}
  
  	public function getName() {
		return $this->firstname;
  	}
  
  	public function getEmail() {
		return $this->email;
  	}
  	
  	public function getDisplayName() {
  		if($this->firstname=='')
  			return $this->email;
  		else
  			return $this->firstname.' '. $this->lastname;
  	}
  	
	public function getZoneId() {
		return $this->zone_id;
  	}
	
	public function getCityId() {
		return $this->city_id;
  	}
  
  	public function getTelephone() {
		return $this->telephone;
  	}
  
  	public function getFax() {
		return $this->fax;
  	}
	
  	public function getNewsletter() {
		return $this->newsletter;	
  	}

  	public function getCustomerGroupId() {
		return $this->customer_group_id;	
  	}
	
  	public function getAddress() {
		return $this->address;	
  	}
	
  	public function getBalance() {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");
	
		return $query->row['total'];
  	}	
		
  	public function getRewardPoints() {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");
	
		return $query->row['total'];	
  	}	
  	
	public function getShippingMethod(){
  		return $this->shipping_method;
  	}
  	
  	public function getCode() {
  		if( $this->code==''){
  			$invite_code = md5(uniqid());
  			$this->db->query("UPDATE " . DB_PREFIX . "customer SET code = '" . $this->db->escape($invite_code)  . "' WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "'");
  			return $invite_code;
  		}else{
  			return $this->code;
  		}
  	}
  	
	public function getShippingMethodTitle(){
  		if($this->shipping_method!=''){
  			$code=explode('.', $this->shipping_method);
			$this->language->load('shipping/'.$code['0']);
  			return $this->language->get('text_title');
		}else{
			return '';
		}
  	}
  	
	public function setShippingMethod($shipping_method){
  		$this->db->query("UPDATE " . DB_PREFIX . "customer SET shipping_method = '".$shipping_method."' WHERE customer_id = '" . (int)$this->customer_id ."'");
  	}
  	
	
	
	public function getPaymentMethod(){
  		return $this->payment_method;
  	}
  	
	public function getPaymentMethodTitle(){
		if($this->payment_method!=''){
			$this->language->load('payment/'.$this->payment_method);
  			return $this->language->get('text_title');
		}else{
			return '';
		}
  	}
  	
  	public function setPaymentMethod($paymentMethod){
  		$this->db->query("UPDATE " . DB_PREFIX . "customer SET payment_method = '".$paymentMethod."'  WHERE customer_id = '" . (int)$this->session->data['customer_id']."'" );
  	}
	
}
?>