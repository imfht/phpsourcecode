<?php 
class ModelShippingWeight extends Model {    
  	public function getQuote($zoneId,$cityId) {
		$this->load->language('shipping/weight');
		
		$quote_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");
	
		foreach ($query->rows as $result) {
			if ($this->config->get('weight_' . $result['geo_zone_id'] . '_status')) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND zone_id = '" . (int)$zoneId . "' AND (city_id = '" . (int)$cityId . "' OR city_id = '0')");
			
				if ($query->num_rows) {
					$status = true;
				} else {
					$status = false;
				}
			} else {
				$status = false;
			}
		
			if ($status) {			
				$cost = $this->config->get('weight_' . $result['geo_zone_id'] . '_rate');

				if ((string)$cost != '') { 
					$quote_data['weight_' . $result['geo_zone_id']] = array(
						'code'         => 'weight.weight_' . $result['geo_zone_id'],
						'title'        => $this->language->get('text_title'). '  (' . $this->language->get('text_weight') . ')',
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('weight_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('weight_tax_class_id'), $this->config->get('config_tax')))
					);	
				}
			}
		}
		
		$method_data = array();
	
		if ($quote_data) {
      		$method_data = array(
        		'code'       => 'weight',
        		'title'      => $this->language->get('text_title'),
      			'description'  => $this->config->get('weight_description'),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('weight_sort_order'),
        		'error'      => false
      		);
		}
	
		return $method_data;
  	}
	
  public function getShippingTime($zoneId,$cityId) {
		$this->load->language('shipping/weight');
		
		$quote_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");
	
		foreach ($query->rows as $result) {
			if ($this->config->get('weight_' . $result['geo_zone_id'] . '_status')) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND zone_id = '" . (int)$zoneId . "' AND (city_id = '" . (int)$cityId . "' OR city_id = '0')");
			
				if ($query->num_rows) {
					$status = true;
				} else {
					$status = false;
				}
			} else {
				$status = false;
			}
		
			if ($status) {			
				$shippingTime = $this->config->get('weight_' . $result['geo_zone_id'] . '_time');
				if($shippingTime){
					return "预计  <font style='color:red;font-weight:bold;'>".$shippingTime."</font> 分钟送达";
					}else{
						return '无预计配送时间';
						}
				
			}
			
		}

	
		
  	}
	
	 public function getCost($zoneId,$cityId) {
		$this->load->language('shipping/weight');
		
		$quote_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");
	
		foreach ($query->rows as $result) {
			if ($this->config->get('weight_' . $result['geo_zone_id'] . '_status')) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND zone_id = '" . (int)$zoneId . "' AND (city_id = '" . (int)$cityId . "' OR city_id = '0')");
			
				if ($query->num_rows) {
					$status = true;
				} else {
					$status = false;
				}
			} else {
				$status = false;
			}
		
			if ($status) {			
				$cost = $this->config->get('weight_' . $result['geo_zone_id'] . '_rate');
				return $cost;
			}
			
		}

	
		
  	}
	
	
	public function getGeoZoneByCityId($city_id) {

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE city_id = " . (int)$city_id);
	
			$geo_zone = $query->row;
	
		    return $geo_zone;
	}
	
	public function getGeoZoneByCityId1($zone_id,$city_id) {

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE  zone_id = '" . (int)$zone_id . "' AND (city_id = '" . (int)$city_id . "' OR city_id = '0')");
	
			$geo_zone = $query->row;
	
		    return $geo_zone;
	}
}
?>