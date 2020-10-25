<?php 
class ControllerCommonLocalisation extends Controller {

  	public function zone() {
		$output = '<option value="">-选择-</option>';
		
		$this->load->model('localisation/zone');

    	$results = $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']);
        
      	foreach ($results as $result) {
        	$output .= '<option value="' . $result['zone_id'] . '"';
	
	    	if ($this->customer->zone_id == $result['zone_id']) {
	      		$output .= ' selected="selected"';
	    	}
	
	    	$output .= '>' . $result['name'] . '</option>';
    	} 
		
		if (!$results) {
		  	$output .= '<option value="">-无-</option>';
		}
	
		$this->response->setOutput($output);
  	}  
  
	public function city() {
		$output = '<option value="">-选择-</option>';

		$this->load->model('localisation/city');

		$results = $this->model_localisation_city->getCitiesByZoneId($this->request->get['zone_id']);

		foreach ($results as $result) {
			$output .= '<option value="' . $result['city_id'] . '"';

			if ($this->customer->city_id == $result['city_id']) {
				$output .= ' selected="selected"';
			}

			$output .= '>' . $result['name'] . '</option>';
		}

		if (!$results) {
			$output = '<option value="">-无-</option>';
		} 

		$this->response->setOutput($output);
	}
	
	public function geo_zone() {

		$this->load->model('shipping/weight');
		
		$results = $this->model_shipping_weight->getGeoZoneByCityId($this->request->get['city_id']);
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$this->request->get['city_id'] . "'");
		$zoneId = $query->row;
		$query1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE zone_id = " .$zoneId['zone_id']);
		$cityZone = $query1->row;
		if(isset($this->request->get['v'])&&$this->request->get['v']==1){
			 if(isset($results['geo_zone_id'])){
					  $output ="预计 <font style='color:red;font-weight:bold;'>".$this->config->get('weight_' . $results['geo_zone_id'] . '_time')."</font> 分钟送达";
							}elseif(isset($cityZone['zone_id'])&&$cityZone['city_id']==0){
								 $output ="预计 <font style='color:red;font-weight:bold;'>".$this->config->get('weight_' . $cityZone['geo_zone_id'] . '_time')."</font> 分钟送达";
								  }else{
										$output='无预计配送时间';
				  }

				  $this->response->setOutput($output);
			}else{
				  if(isset($results['geo_zone_id'])){
					  $output =$this->config->get('weight_' . $results['geo_zone_id'] . '_rate');
							}elseif(isset($cityZone['zone_id'])&&$cityZone['city_id']==0){
								  $output =$this->config->get('weight_' . $cityZone['geo_zone_id'] . '_rate');;
								  }else{
										$output='0.00';
				  }

				  $this->response->setOutput($output);

			}
	}
	
}
?>