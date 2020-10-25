<?php
class ModelCheckoutOrder extends Model {	
	public function create($data) {
		$common=new Common($this->registry);
		
		$order_id  = $common->genOrderSN();
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET  order_id ='".$order_id."' ,invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url'])."' ,type = '" . $this->db->escape($data['type'])."' ,seat = '" . $this->db->escape($data['seat'])  . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', telephone = '" . $this->db->escape($data['telephone']). "', zone_id = '" . $this->db->escape($data['zone_id']). "', city_id = '" . $this->db->escape($data['city_id']). "', address = '" . $this->db->escape($data['address']). "',  comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', reward = '" . (float)$data['reward']. "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', date_added = NOW(), date_modified = NOW()");

		foreach ($data['products'] as $product) { 
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . $order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity1'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "'");
 
			$order_product_id = $this->db->getLastId();
		}
		
		foreach ($data['totals'] as $total) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . $order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
		}	
		
	
      if($data['type']==2){
		  $this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']). "', telephone = '" . $this->db->escape($data['telephone']). "', zone_id = '" . $this->db->escape($data['zone_id']). "', city_id = '" . $this->db->escape($data['city_id']). "', address = '" . $this->db->escape($data['address']). "' WHERE customer_id = '" . (int)$data['customer_id'] . "'");
		  
	  }else{
		    $this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']). "', telephone = '" . $this->db->escape($data['telephone']). "' WHERE customer_id = '" . (int)$data['customer_id'] . "'");
	  }
		  
		return $order_id;
    }
		
	public function updateOrderStatus($order_id,$order_status_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . $order_status_id . "',  date_modified = NOW() WHERE  order_id ='".$order_id."'");
	}
	
	public function updateOrderComment($order_id,$comment) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET comment = '" . $this->db->escape($comment) . "',  date_modified = NOW() WHERE  order_id ='".$order_id."'");
	}
	
	public function updateOrderPayment($order_id,$payment){
		$sql="UPDATE `" . DB_PREFIX . "order` SET payment_code = '" . $this->db->escape($payment['code']) . "',  payment_method = '" . $this->db->escape($payment['title']) . "'WHERE  order_id ='".$order_id."'";
		$this->db->query($sql);
	}
	
	public function updateOrderShipping($order_id,$shipping){
		$sql="UPDATE `" . DB_PREFIX . "order` SET shipping_method = '" . $this->db->escape($shipping['title']) . "'WHERE  order_id ='".$order_id."'";
		$this->db->query($sql);
	}
    public function getOpenid() {

    		$openid_query = $this->db->query("SELECT openid FROM `" . DB_PREFIX . "user`");
    		return $openid_query->rows;

    }
    public function getEmail() {

    		$email_query = $this->db->query("SELECT email FROM `" . DB_PREFIX . "user`");
    		return $email_query->rows;

    }
	
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . $order_id . "'");
			
		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';				
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}
			
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';				
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$this->load->model('localisation/language');
			
			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);
			
			if ($language_info) {
				$language_code = $language_info['code'];
				$language_filename = $language_info['filename'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = '';
				$language_filename = '';
				$language_directory = '';
			}
		 			
			return array(
				'order_id'                => $order_id,
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],				
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],				
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],	
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'order_status'            => $order_query->row['order_status'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'language_filename'       => $language_filename,
				'language_directory'      => $language_directory,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']
			);
		} else {
			return false;	
		}
	}	

	public function confirm($order_id, $order_status_id, $comment = '') {
		$order_info = $this->getOrder($order_id);
		
		$this->log->debug('IlexDebug:: Checkout order confirm() : order_id '.$order_id.' | order_status '.$order_status_id);
		
		if ($order_info) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");
	
			if ($query->row['invoice_no']) {
				$invoice_no = (int)$query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}
			
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "', order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . $order_id . "'");

		//	$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . $order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '1', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");

			$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . $order_id . "'");
			
			foreach ($order_product_query->rows as $order_product) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");
			}
			
			$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . $order_id . "'");
			
			foreach ($order_total_query->rows as $order_total) {
				$this->load->model('total/' . $order_total['code']);
				
				if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
					$this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
				}
			}
			// Send out order confirmation mail
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_filename']);
			$language->load('mail/order');
		 
			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
			
			if ($order_status_query->num_rows) {
				$order_status = $order_status_query->row['name'];	
			} else {
				$order_status = '';
			}

            if ($this->config->get('config_alert_mail')) {
			// Admin Alert Mail
				$subject = "订单：".$order_id;
				
				// Text 
				$text  = $language->get('text_new_received') . "<br><br>";
				$text .= $language->get('text_new_order_id') . ' ' . $order_id . "<br>";
				$text .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "<br>";
				$text .= $language->get('text_new_order_status') . ' ' . $order_status . "<br><br>";
				$text .= $language->get('text_new_products') . "<br>";
				
				foreach ($order_product_query->rows as $result) {
					$text .= $result['quantity'] . 'x ' . $result['name'] . html_entity_decode($this->currency->format($result['total'], $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "<br>";
				}
				
				$text .= "<br><br>";

				$text.= $language->get('text_new_order_total') . "<br>";
				
				foreach ($order_total_query->rows as $result) {
					$text .= $result['title'] . ' ' . html_entity_decode($result['text'], ENT_NOQUOTES, 'UTF-8') . "<br>";
				}			
				
				$text .= "<br>";
				$orderurl = $this->config->get('config_url')."weixin/index.php?route=sale/order/info&order_id=".$order_id;
				$text .= "<a href=".$orderurl."><img src=".$this->config->get('config_url')."catalog/view/theme/diancan/image/chuli.gif></a> <br>";
			
				$mail = new Mail(); 
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->hostname = $this->config->get('config_smtp_host');
				$mail->username = $this->config->get('config_smtp_username');
				$mail->password = $this->config->get('config_smtp_password');
				$mail->port = $this->config->get('config_smtp_port');
				$mail->timeout = $this->config->get('config_smtp_timeout');
				$mail->setTo($this->config->get('config_email'));
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender("订单推送");
				$mail->setSubject($subject);
				$mail->setHtml($text);
				$mail->send();
				
				// Send to additional alert emails
				$emails = $this->getEmail();
				if($emails){
					foreach ($emails as $email) {
						if ($email['email'] && preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i', $email['email'])) {
							$mail->setTo($email['email']);
							$mail->send();
						}
				    }		
			    }
			}
						
			
			
			
			
	}
		}
	public function update($order_id, $order_status_id, $comment = '', $notify = false) {
		$order_info = $this->getOrder($order_id);

		if ($order_info && $order_info['order_status_id']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . $order_id . "'");
		
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . $order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	
			// Send out any gift voucher mails
			if ($this->config->get('config_complete_status_id') == $order_status_id) {
				$this->load->model('checkout/voucher');
	
				$this->model_checkout_voucher->confirm($order_id);
			}	
		}
	}
}
?>