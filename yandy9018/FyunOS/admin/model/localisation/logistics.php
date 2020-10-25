<?php 
class ModelLocalisationLogistics extends Model {
	public function addLogistics($data) {
		foreach ($data['logistics'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "logistics SET language_id = '" . (int)$language_id . "', logistics_name = '" . $this->db->escape($value['logistics_name']) . "',logistics_link = '" . $this->db->escape($value['logistics_link']) . "'");
		}
		
		$this->cache->delete('logistics');
	}

	public function editLogistics($logistics_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "logistics WHERE logistics_id = '" . (int)$logistics_id . "'");

		foreach ($data['logistics'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "logistics SET language_id = '" . (int)$language_id . "', logistics_name = '" . $this->db->escape($value['logistics_name']) . "',logistics_link = '" . $this->db->escape($value['logistics_link']) . "'");
		}
				
		$this->cache->delete('logistics');
	}
	
	public function deleteLogistics($logistics_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "logistics WHERE logistics_id = '" . (int)$logistics_id . "'");
	
		$this->cache->delete('logistics');
	}
		
	public function getLogistics($logistics_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "logistics WHERE logistics_id = '" . (int)$logistics_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
	
	public function getLogisticses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "logistics WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
      		
			$sql .= " ORDER BY logistics_name";	
			
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
			$logistics_data = $this->cache->get('logistics.' . $this->config->get('config_language_id'));
		
			if (!$logistics_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "logistics WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY logistics_name");
	
				$logistics_data = $query->rows;
			
				$this->cache->set('logistics.' . $this->config->get('config_language_id'), $logistics_data);
			}	
	
			return $logistics_data;			
		}
	}
	
	public function getLogisticsDescriptions($logistics_id) {
		$logistics_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "logistics WHERE logistics_id = '" . (int)$logistics_id . "'");
		
		foreach ($query->rows as $result) {
			$logistics_data[$result['language_id']] = array('logistics_name' => $result['logistics_name'],'logistics_link' => $result['logistics_link']);
		}
		
		return $logistics_data;
	}
	
	public function getTotalLogistics() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "logistics WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row['total'];
	}	
}
?>