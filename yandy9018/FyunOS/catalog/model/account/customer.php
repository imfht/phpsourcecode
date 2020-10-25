<?php
class ModelAccountCustomer extends Model {
	public function addCustomer($data) {
		$active_code = md5(uniqid());
		$status=1;
		if($this->config->get('config_active')=='1'){
			$status=0;
		}
      	$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET active_code = '" . $active_code . "', store_id = '" . (int)$this->config->get('config_store_id') . "',  email = '" . $this->db->escape($data['email']) . "',password = '" . $this->db->escape(md5($data['password'])) . "', newsletter = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', status = '" . (int)$status . "', date_added = NOW()");
      	
		$customer_id = $this->db->getLastId();
		$this->language->load('mail/customer');
		
		if(isset($data['invite_code']) && $data['invite_code']!='0'){
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE code = '" . $this->db->escape($data['invite_code']). "' AND status = '1'");
			$send_id=0;
			if ($customer_query->num_rows) {
				
				$send_id = $customer_query->row['customer_id'];
			
				$this->db->query("INSERT INTO " . DB_PREFIX . "invited_history SET invited_id = '" . (int)$customer_id . "',  customer_id = '" . (int)$send_id . "',  date_modified= NOW() , date_added = NOW()");
				if($this->config->get('config_active')!='1'&&(int)$this->config->get('config_invite_points')>0){
					$this->addReward($send_id,$this->language->get('text_reward_system'),$this->config->get('config_invite_points'));
				}
			}
		}
		
      	if (!$this->config->get('config_customer_approval')) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET approved = '1' WHERE customer_id = '" . (int)$customer_id . "'");
		}	
		if($this->config->get('config_active')=='1'){
			$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));
		}else{
			$subject = sprintf($this->language->get('text_subject1'), $this->config->get('config_name'));
		}
		$message = sprintf($this->language->get('text_welcome'), $this->config->get('config_name')) . "\n\n";
		
		if (!$this->config->get('config_customer_approval')) {
			if($this->config->get('config_active')=='1'){
				$message .= $this->language->get('text_active') . "\n";
			}else{
				$message .= $this->language->get('text_login') . "\n";
			}
		} else {
			$message .= $this->language->get('text_approval') . "\n";
		}
		
		if($this->config->get('config_active')=='1'){
			$message .= $this->url->link('account/active&active_code='.$active_code, '', 'SSL') . "\n\n";
			$message .= $this->language->get('text_services') . "\n\n";
			$message .= $this->language->get('text_thanks') . "\n";
			$message .= $this->config->get('config_name');
		}else{
			$message .= $this->url->link('account/login', '', 'SSL') . "\n\n";
			$message .= $this->language->get('text_services') . "\n\n";
			$message .= $this->language->get('text_thanks') . "\n";
			$message .= $this->config->get('config_name');
		}
		
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');				
		$mail->setTo($this->request->post['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject($subject);
		$mail->setText($message);
		$mail->send();
		
		// Send to main admin email if new account email is enabled
		if ($this->config->get('config_account_mail')) {
			$mail->setTo($this->config->get('config_email'));
			$mail->send();
			
			// Send to additional alert emails if new account email is enabled
			$emails = explode(',', $this->config->get('config_alert_emails'));
			
			foreach ($emails as $email) {
				if (strlen($email) > 0 && preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i', $email)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}
		return $customer_id;
	}
	
	
	public function addWeixinCustomer($data) {
		$active_code = md5(uniqid());
		$status=1;
		if($this->config->get('config_active')=='1'){
			$status=0;
		}
      	$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET active_code = '" . $active_code . "', store_id = '" . (int)$this->config->get('config_store_id') . "',  open_id = '" . $data['openid'] . "',firstname = '" . $data['nickname'] . "', newsletter = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', status = '" . (int)$status . "', date_added = NOW()");
		return 1;
	}
	
	public function DeleteWeixinOpenid($open_id) {
		$this->db->query("DELETE from " . DB_PREFIX . "customer WHERE open_id = '" .$open_id. "'");
	}
	
	public function addReward($customer_id, $description = '', $points = '') {
		$customer_info = $this->getCustomer($customer_id);
			
		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', points = '" . (int)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");
	
			$this->language->load('mail/customer');
				
			$message  = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
			$message .= sprintf($this->language->get('text_reward_total'), $this->getRewardTotal($customer_id));
	
			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');
			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject(sprintf($this->language->get('text_reward_subject'), $this->config->get('config_name')));
			$mail->setText($message);
			$mail->send();
		}
	}
	
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
	
		return $query->row;
	}
	
	public function getCustomerByTelephone($telephone) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE telephone = '" . $telephone . "'");


			if($query->row['customer_id']!=(int)$this->customer->getId()){
				$this->db->query("UPDATE " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$this->customer->getId(). "' WHERE customer_id = '" . (int)$query->row['customer_id'] . "'");
				
				$this->db->query("UPDATE " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$this->customer->getId(). "' WHERE customer_id = '" . (int)$query->row['customer_id'] . "'");
				
				$this->db->query("UPDATE " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$this->customer->getId(). "' WHERE customer_id = '" . (int)$query->row['customer_id'] . "'");
			    
				$this->db->query("UPDATE " . DB_PREFIX . "order SET customer_id = '" . (int)$this->customer->getId(). "' WHERE customer_id = '" . (int)$query->row['customer_id'] . "'");
				
				$this->db->query("DELETE from " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$query->row['customer_id'] . "'");
			
				}
		
	}
	
	public function getRewardTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
	
		return $query->row['total'];
	}
	
	public function activeCustomer($code) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET status = '1' WHERE active_code = '" . $this->db->escape($code). "'");
		
		$customer_query = $this->db->query("SELECT ih.* FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "invited_history ih  ON ih.invited_id=c.customer_id WHERE c.active_code = '" . $this->db->escape($code). "' AND c.status = '1'");
		$send_id=0;
		if ($customer_query->num_rows) {
			$this->language->load('mail/customer');
			$send_id = $customer_query->row['customer_id'];
			$this->addReward($send_id,$this->language->get('text_reward_system'),$this->config->get('config_invite_points'));
		}
	}
	
	public function editCustomer($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']). "', address = '" . $this->db->escape($data['address']). "', zone_id = '" . $this->db->escape($data['zone_id']). "', city_id = '" . $this->db->escape($data['city_id']). "', telephone = '" . $this->db->escape($data['telephone']). "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editPassword($email, $password) {
      	$this->db->query("UPDATE " . DB_PREFIX . "customer SET password = '" . $this->db->escape(md5($password)) . "' WHERE email = '" . $this->db->escape($email) . "'");
	}

	public function editNewsletter($newsletter) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}
				
	
	public function getInvitedHistory($customer_id) {
		$query=$this->db->query("SELECT * FROM " . DB_PREFIX . "invited_history WHERE customer_id = '" . (int)$customer_id . "'");
		$invited="";
		if($query->num_rows){
			$invited='';
			$count=1;
			foreach ($query->rows as $row) {
				if($count==1)
				$invited.=$row['invited_id'];
				else
				$invited.=','.$row['invited_id'];
				$count++;
			}
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id IN (" .$invited . ") AND status = '1'");
			return $customer_query->rows;
		}else{
			return 0;
		}
	}
	
	public function getCustomerGroup($customer_group_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		
		return $query->row['name'];
	}
	
	public function getCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cg.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group cg ON (c.customer_group_id = cg.customer_group_id) ";

		$implode = array();
		
		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$implode[] = "LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '" . $this->db->escape(mb_strtolower($data['filter_name'], 'UTF-8')) . "%'";
		}
		
		if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
			$implode[] = "c.email = '" . $this->db->escape($data['filter_email']) . "'";
		}
		
		if (isset($data['filter_customer_group_id']) && !is_null($data['filter_customer_group_id'])) {
			$implode[] = "cg.customer_group_id = '" . $this->db->escape($data['filter_customer_group_id']) . "'";
		}	
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}	
		
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}	
			
		if (isset($data['filter_ip']) && !is_null($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}	
				
		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.ip',
			'c.date_added'
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY name";	
		}
			
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}		
		
		$query = $this->db->query($sql);
		
		return $query->rows;	
	}
		
	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
		
		return $query->row['total'];
	}
	// FIXME remove this method, we dun need it anymore
	public function editShippingMethod($shipping_method){
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET shipping_method = '" . $this->db->escape($shipping_method) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}
	
	public function editPaymentMethod($payment_method){
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET payment_method = '" . $this->db->escape($payment_method) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}
}
?>