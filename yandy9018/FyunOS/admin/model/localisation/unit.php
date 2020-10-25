<?php 
class ModelLocalisationUnit extends Model {
	public function addUnit($data) {
		foreach ($data['unit'] as $language_id => $value) {
			if (isset($unit_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "unit SET unit_id = '" . (int)$unit_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "unit SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
				
				$unit_id = $this->db->getLastId();
			}
		}
		
		$this->cache->delete('unit');
	}

	public function editUnit($unit_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "unit WHERE unit_id = '" . (int)$unit_id . "'");

		foreach ($data['unit'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "unit SET unit_id = '" . (int)$unit_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
				
		$this->cache->delete('unit');
	}
	
	public function deleteUnit($unit_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "unit WHERE unit_id = '" . (int)$unit_id . "'");
	
		$this->cache->delete('unit');
	}
		
	public function getUnit($unit_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "unit WHERE unit_id = '" . (int)$unit_id . "'");
		
		return $query->row;
	}
		
	public function getUnits($data = array()) {
      	if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "unit WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
			$sql .= " ORDER BY name";	
			
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
		} else {
			$unit_data = $this->cache->get('unit.' . $this->config->get('config_language_id'));
		
			if (!$unit_data) {
				$query = $this->db->query("SELECT unit_id, name FROM " . DB_PREFIX . "unit WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
	
				$unit_data = $query->rows;
			
				$this->cache->set('unit.' . $this->config->get('config_language_id'), $unit_data);
			}	
	
			return $unit_data;				
		}
	}
	
	public function getUnitDescriptions($unit_id) {
		$unit_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "unit WHERE unit_id = '" . (int)$unit_id . "'");
		
		foreach ($query->rows as $result) {
			$unit_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $unit_data;
	}
	
	public function getTotalUnits() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "unit WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row['total'];
	}	
}
?>