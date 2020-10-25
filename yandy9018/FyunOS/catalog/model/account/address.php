<?php
class ModelAccountAddress extends Model {
	public function addAddress($data,$customer_id=0) {
		if($customer_id==0){
			$customer_id=(int)$this->customer->getId();
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . $customer_id . "',  firstname = '" . $this->db->escape($data['firstname']) . "',address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city_id = '" . (int)$data['city_id'] . "', zone_id = '" . (int)$data['zone_id'] . "'," .
				"mobile = '" .  $this->db->escape($data['mobile']) . "', phone = '" .  $this->db->escape($data['phone']) . "', country_id = '" . (int)$this->config->get('config_country_id') . "'");
		
		$address_id = $this->db->getLastId();
		
		$addresses=$this->getAddresses();
		// 如果账户下没有地址就默认的添加新的为默认地址
		if($addresses){
			if (isset($data['default']) && $data['default'] == '1') {
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
			}
		}else{
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
		
		return $address_id;
	}
	
	public function editAddress($address_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "address SET  firstname = '" . $this->db->escape($data['firstname']) . "',  address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city_id = '" . (int)$data['city_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$this->config->get('config_country_id') . "' ,mobile = '" .  $this->db->escape($data['mobile']) . "', phone = '" .  $this->db->escape($data['phone']) . "'  WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	
		if (isset($data['default']) && $data['default'] == '1') {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
	}
	
	public function deleteAddress($address_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	}	
	
	public function getAddress($address_id) {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$code = $zone_query->row['code'];
			} else {
				$zone = '';
				$code = '';
			}		
			
			$city_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "city` WHERE city_id = '" . (int)$address_query->row['city_id'] . "'");
				
			if ($city_query->num_rows) {
				$city = $city_query->row['name'];
				$city_code = $city_query->row['code'];
			} else {
				$city = '';
				$city_code = '';
			}
			
			$address_data = array(
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city_id'        => $address_query->row['city_id'],
				'city'           => $city,
				'city_code'      => $city_code,
				'mobile'         => $address_query->row['mobile'],
				'phone'          => $address_query->row['phone'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
			
			return $address_data;
		} else {
			return false;	
		}
	}
	
	public function getAddresses() {
		$address_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	
		foreach ($query->rows as $result) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$result['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$code = $zone_query->row['code'];
			} else {
				$zone = '';
				$code = '';
			}		
		
			$city_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "city` WHERE city_id = '" . (int)$result['city_id'] . "'");
				
			if ($city_query->num_rows) {
				$city = $city_query->row['name'];
				$city_code = $city_query->row['code'];
			} else {
				$city = '';
				$city_code = '';
			}
			
			$address_data[] = array(
				'address_id'     => $result['address_id'],
				'firstname'      => $result['firstname'],
				'lastname'       => $result['lastname'],
				'company'        => $result['company'],
				'address_1'      => $result['address_1'],
				'address_2'      => $result['address_2'],
				'postcode'       => $result['postcode'],
				'city_id'        => $result['city_id'],
				'city'           => $city,
				'city_code'      => $city_code,
				'mobile'         => $result['mobile'],
				'phone'          => $result['phone'],
				'zone_id'        => $result['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $code,
				'country_id'     => $result['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		}		
		
		return $address_data;
	}	
	
	public function getTotalAddresses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	
		return $query->row['total'];
	}
	
	/**
	 * editDefaultAddress($address_id)		update customer default address id
	 * @param $address_id int
	 * */
	public function editDefaultAddress($address_id){
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}
}
?>