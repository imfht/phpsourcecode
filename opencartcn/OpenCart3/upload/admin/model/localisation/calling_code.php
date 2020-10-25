<?php
class ModelLocalisationCallingCode extends Model {
	public function addCallingCode($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "calling_code SET name = '" . $this->db->escape((string)$data['name']) . "', code = '" . $this->db->escape((string)$data['code']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = '" . date('Y-m-d H:i:s') . "', date_added = '" . date('Y-m-d H:i:s') . "'");

		$this->cache->delete('calling_code');
		
		return $this->db->getLastId();
	}

	public function editCallingCode($calling_code_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "calling_code SET name = '" . $this->db->escape((string)$data['name']) . "', code = '" . $this->db->escape((string)$data['code']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = '" . date('Y-m-d H:i:s') . "' WHERE calling_code_id = '" . (int)$calling_code_id . "'");

		$this->cache->delete('calling_code');
	}

	public function deleteCallingCode($calling_code_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "calling_code WHERE calling_code_id = '" . (int)$calling_code_id . "'");

		$this->cache->delete('calling_code');
	}

	public function getCallingCode($calling_code_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "calling_code WHERE calling_code_id = '" . (int)$calling_code_id . "'");

		return $query->row;
	}

	public function getCallingCodes($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "calling_code";

			$sort_data = array(
				'name',
				'code',
				'sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
			}

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
			$calling_code_data = $this->cache->get('calling_code.admin');

			if (!$calling_code_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "calling_code ORDER BY sort_order, name ASC");

				$calling_code_data = $query->rows;

				$this->cache->set('calling_code.admin', $calling_code_data);
			}

			return $calling_code_data;
		}
	}

	public function getTotalCallingCodes() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "calling_code");

		return $query->row['total'];
	}
}