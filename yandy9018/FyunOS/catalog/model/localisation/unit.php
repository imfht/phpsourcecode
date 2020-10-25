<?php 
class ModelLocalisationUnit extends Model {	
	public function getUnit($unit_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "unit WHERE unit_id = '" . (int)$unit_id . "'");
		
		return $query->row;
	}
}
?>