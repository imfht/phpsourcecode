<?php
class ModelCatalogSitemap extends Model {
	
		
	public function getInformations($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
			$sort_data = array(
				'id.title',
				'i.sort_order'
			);		
		
			if (in_array(@$data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY id.title";	
			}
			
			if (@$data['order'] == 'DESC') {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
			
			if (isset($data['start']) || isset($data['limit'])) {
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		} else {
			$information_data = $this->cache->get('information.' . (int)$this->config->get('config_language_id'));
		
			if (!$information_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");
	
				$information_data = $query->rows;
			
				$this->cache->set('information.' . (int)$this->config->get('config_language_id'), $information_data);
			}	
	
			return $information_data;			
		}
	}
	
	public function getCategorieForSitemap($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order ,c.date_modified");

		return $query->rows;
	}
	
	
	public function getAllProducts() {
		$query =$this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE  pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		return $query->rows;
	}
}
?>