<?php
final class Authorize {	
  	public function verification() {
		$authorize = $this->information();
    	if (time()>$authorize['expiry_date']) {
      		return false;
    	} else {
      		return true;
    	}
  	}

  	public function information() {
		return $information = array(
        			'authorize_name'   => '罗曼蒂克餐厅',
        			'administrator'      => '王帆',
        			'expiry_date'            => 1429192534,
      			);
  	}
}
?>