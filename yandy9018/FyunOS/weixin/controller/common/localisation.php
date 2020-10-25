<?php 
class ControllerCommonLocalisation extends Controller {

  	public function zone() {
		$output = '<option value="">' . $this->language->get('text_select') . '</option>';
		
		$this->load->model('localisation/zone');

    	$results = $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']);
        
      	foreach ($results as $result) {
        	$output .= '<option value="' . $result['zone_id'] . '"';
	
	    	if (isset($this->request->get['zone_id']) && ($this->request->get['zone_id'] == $result['zone_id'])) {
	      		$output .= ' selected="selected"';
	    	}
	
	    	$output .= '>' . $result['name'] . '</option>';
    	} 
		
		if (!$results) {
		  	$output .= '<option value="">' . $this->language->get('text_none') . '</option>';
		}
	
		$this->response->setOutput($output);
  	}  
  
	public function city() {
		$output = '<option value="">' . $this->language->get('text_select') . '</option>';

		$this->load->model('localisation/city');

		$results = $this->model_localisation_city->getCitiesByZoneId($this->request->get['zone_id']);

		foreach ($results as $result) {
			$output .= '<option value="' . $result['city_id'] . '"';

			if (isset($this->request->get['city_id']) && ($this->request->get['city_id'] == $result['city_id'])) {
				$output .= ' selected="selected"';
			}

			$output .= '>' . $result['name'] . '</option>';
		}

		if (!$results) {
			$output = '<option value="">' . $this->language->get('text_none') . '</option>';
		} 

		$this->response->setOutput($output);
	}
}
?>