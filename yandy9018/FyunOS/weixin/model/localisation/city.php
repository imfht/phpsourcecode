<?php
class ModelLocalisationCity extends Model {

	public function addCity($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "city SET status = '" . (int)$data['status'] . "',name = '" . $this->db->escape($data['name']). "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "'");
			
		$this->cache->delete('city');
	}

	public function editCity($city_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "city SET status = '" . (int)$data['status'] . "',name = '" . $this->db->escape($data['name']). "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "' WHERE city_id = '" . (int)$city_id . "'");

		$this->cache->delete('city');
	}

	public function deleteCity($city_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$city_id . "'");

		$this->cache->delete('city');	
	}

//	public function getCity($city_id) {
//		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "v_city WHERE city_id = '" . (int)$city_id . "'");
//		
//		return $query->row;
//	}
	
	public function getCity($city_id) {
		$query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$city_id . "'");
		
		return $query->row;
	}
	
	public function getCities($data = array()) {
		$sql = "SELECT * FROM (
		SELECT `a`.`city_id` AS `city_id`
		     , `a`.`zone_id` AS `city_zone_id`
		     , `b`.`name` AS `city_zone`
		     , `a`.`name` AS `city_name`
		     , `a`.`code` AS `city_code`
		     , `b`.`country_id` AS `city_country_id`
		     , `c`.`name` AS `city_country`
		     , `a`.`status` AS `city_status`
			FROM ((`" . DB_PREFIX . "city` `a`
			LEFT JOIN `" . DB_PREFIX . "zone` `b`
			ON ((`a`.`zone_id` = `b`.`zone_id`)))
			LEFT JOIN `" . DB_PREFIX . "country` `c`
			ON ((`b`.`country_id` = `c`.`country_id`)))
			) AS v_city";

		$implode = array();
		
		if (isset($data['filter_zone']) && !is_null($data['filter_zone'])) {
			$implode[] = "city_zone LIKE '%" . $this->db->escape($data['filter_zone']) . "%'";
		}

		if (isset($data['filter_city']) && !is_null($data['filter_city'])) {
			$implode[] = "city_name LIKE '%" . $this->db->escape($data['filter_city']) . "%'";
		}

		if (isset($data['filter_code']) && !is_null($data['filter_code'])) {
			$implode[] = "city_code LIKE '%" . $this->db->escape($data['filter_code']) . "%'";
		}

		if (isset($data['filter_country_id']) && !is_null($data['filter_country_id'])) {
			$implode[] = "city_country_id = '" . $this->db->escape($data['filter_country_id']) . "'";
		}
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "city_status = '" . (int)$data['filter_status'] . "'";
		}	
				
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$sort_data = array(
			'city_zone',
			'city_id',
			'city_name',
			'city_code',
			'city_country',
			'city_status'
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY city_zone";	
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
	}

	public function getCitiesByZoneId($zone_id) {
		$city_data = $this->cache->get('city.' . $zone_id);
	
		if (!$city_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city WHERE zone_id = '" . (int)$zone_id . "' AND status = '1' ORDER BY convert(name using gb2312) ");
	
			$city_data = $query->rows;
			
			$this->cache->set('city.' . $zone_id, $city_data);
		}
	
		return $city_data;
	}

	public function getTotalCities($data = array()) {
      	$sql = "SELECT COUNT(*) AS total FROM (
			SELECT `a`.`city_id` AS `city_id`
		     , `a`.`zone_id` AS `city_zone_id`
		     , `b`.`name` AS `city_zone`
		     , `a`.`name` AS `city_name`
		     , `a`.`code` AS `city_code`
		     , `b`.`country_id` AS `city_country_id`
		     , `c`.`name` AS `city_country`
		     , `a`.`status` AS `city_status`
			FROM ((`" . DB_PREFIX . "city` `a`
			LEFT JOIN `" . DB_PREFIX . "zone` `b`
			ON ((`a`.`zone_id` = `b`.`zone_id`)))
			LEFT JOIN `" . DB_PREFIX . "country` `c`
			ON ((`b`.`country_id` = `c`.`country_id`)))
			) AS v_city";
		
		$implode = array();

		if (isset($data['filter_zone']) && !is_null($data['filter_zone'])) {
			$implode[] = "city_zone LIKE '%" . $this->db->escape($data['filter_zone']) . "%'";
		}

		if (isset($data['filter_city']) && !is_null($data['filter_city'])) {
			$implode[] = "city_name LIKE '%" . $this->db->escape($data['filter_city']) . "%'";
		}

		if (isset($data['filter_code']) && !is_null($data['filter_code'])) {
			$implode[] = "city_code LIKE '%" . $this->db->escape($data['filter_code']) . "%'";
		}

		if (isset($data['filter_country_id']) && !is_null($data['filter_country_id'])) {
			$implode[] = "city_country_id = '" . $this->db->escape($data['filter_country_id']) . "'";
		}
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "city_status = '" . (int)$data['filter_status'] . "'";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
				
		$query = $this->db->query($sql);
				
		return $query->row['total'];
	}

	public function getTotalCitiesByZoneId($zone_id) {
		$query = $this->db->query("SELECT count(*) AS total FROM " . DB_PREFIX . "city WHERE zone_id = '" . (int)$zone_id . "'");
	
		return $query->row['total'];
	}

}
?>