<?php
class ModelCatalogInformation extends Model {
	public function getInformation($information_id) {
		$information_cache= array('information_id'  => $information_id);
		$cache = md5(http_build_query($information_cache));
		
		$information_data = $this->cache->get('information.' . $cache.'.'.$information_id );
		
		if(!$information_data){
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");
			$this->cache->set('information.' . $cache.'.'.$information_id , $query->row);
			return $query->row;
		}else{
			return $information_data;
		}
	}
	
	public function getInformations() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' AND i.sort_order <> '-1' ORDER BY i.sort_order, LCASE(id.title) ASC");
		
		return $query->rows;
	}
	
	public function addMessage($data) {
		$sql="INSERT INTO " . DB_PREFIX . "message SET author = '" . $data['name']
		. "', email = '" . $data['email']
		. "', message = '" . $this->db->escape($data['enquiry'])
		. "', date_modified = NOW(), date_added = NOW()";
		$this->db->query($sql);
	}
	
	public function getMessages() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "message ORDER BY date_added DESC limit 0,9");
		
		return $query->rows;
	}
	
	public function getInformationLayoutId($information_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		 
		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return $this->config->get('config_layout_information');
		}
	}	
}
?>