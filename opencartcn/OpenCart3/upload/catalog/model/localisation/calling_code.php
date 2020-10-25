<?php
class ModelLocalisationCallingCode extends Model {
	public function getCallingCode($calling_code_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "calling_code WHERE calling_code_id = '" . (int)$calling_code_id . "' AND status = '1'");

		return $query->row;
	}

	public function getCallingCodes() {
		$calling_code_data = $this->cache->get('calling_code.catalog');

		if (!$calling_code_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "calling_code WHERE status = '1' ORDER BY sort_order, name ASC");

			$calling_code_data = $query->rows;

			$this->cache->set('calling_code.catalog', $calling_code_data);
		}

		return $calling_code_data;
	}
}