<?php
class ModelCatalogNav extends Model {	
	public function getNavs($data = array()) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "nav where status=1 ORDER BY sort_order DESC");
		return $query->rows;
	}
	public function getHomeNavs($data = array()) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "nav where status=1 and ishome=1 ORDER BY sort_order DESC");
		return $query->rows;
	}
	
}
?>