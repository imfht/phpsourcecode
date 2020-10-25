<?php
class ModelCatalogNav extends Model {
	public function addNav($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "nav SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', title = '" . $data['title'] . "', tid = '" . $data['tid'] . "',ishome = '" . (int)$data['ishome'] . "', url = '" . $data['url'] . "'");
	}
	
	public function editNav($nav_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "nav SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', title = '" . $data['title'] . "', tid = '" . $data['tid'] . "',ishome = '" . (int)$data['ishome'] . "', url = '" . $data['url'] . "' WHERE nav_id = '" . (int)$nav_id . "'");

	}
	
	public function deleteNav($nav_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "nav WHERE nav_id = '" . (int)$nav_id . "'");
	}	

	public function getNav($nav_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "nav WHERE nav_id = '" . (int)$nav_id . "'");
		
		return $query->row;
	}
		
	public function getNavs($data = array()) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "nav ORDER BY sort_order DESC");
		return $query->rows;
	}
	
}
?>