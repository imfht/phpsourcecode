<?php

/**
 * description
 *
 * @copyright        2017/11/29 opencart.cn - All Rights Reserved
 * @link             http://www.guangdawangluo.com
 * @author           Eric Yang <yangyw@opencart.cn>
 * @created          2017/11/29 14:23
 * @modified         2017/11/29 14:23
 */
class ModelLocalisationCity extends Model
{

    public function addCity($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "city SET name = '" . $this->db->escape($data['name']) . "', up_id='" . (int)$data['parent_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', status = '" . (int)$data['status'] . "'");

        $this->cache->delete('city');
    }

    public function editCity($city_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "city SET name = '" . $this->db->escape($data['name']) . "', up_id='" . (int)$data['parent_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', status = '" . (int)$data['status'] . "' WHERE city_id = '" . (int)$city_id . "'");

        $this->cache->delete('city');
    }

    public function deleteCity($city_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$city_id . "'");

        $this->cache->delete('city');
    }

    public function getCity($city_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$city_id . "'");

        return $query->row;
    }

    public function getCitiesByZoneId($zoneId, $topLevel = true)
    {
        $cacheKey = 'city.' . (int)$zoneId . '_' . (int)$topLevel;
        $city_data = $this->cache->get($cacheKey);

        if (!$city_data) {
            if ($topLevel) {
                $sql = "SELECT * FROM " . DB_PREFIX . "city WHERE zone_id = '" . (int)$zoneId . "' AND status = '1' AND up_id=0 ORDER BY name";
            } else {
                $sql = "SELECT * FROM " . DB_PREFIX . "city WHERE up_id = '" . (int)$zoneId . "' AND status = '1' ORDER BY name";
            }
            $query = $this->db->query($sql);

            $city_data = $query->rows;
            $this->cache->set($cacheKey, $city_data);
        }

        return $city_data;
    }

    public function getCities($data = array())
    {
        if ($data) {
            $sql = "SELECT c.city_id AS city_id, c.zone_id AS zone_id, c.name AS name, c.status AS status, (SELECT name FROM " . DB_PREFIX . "zone z WHERE z.zone_id = c.zone_id AND z.status = '1') AS zone FROM " . DB_PREFIX . "city c";
            if (isset($data['filter_name']) && $data['filter_name']) {
                $sql .= " where c.name like '%" . $this->db->escape((string)$data['filter_name']) . "%'";
            }
            $sort_data = array(
                'name',
                'zone'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY zone";
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
            $city_data = $this->cache->get('city');

            if (!$city_data) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city ORDER BY name ASC");

                $city_data = $query->rows;

                $this->cache->set('city', $city_data);
            }

            return $city_data;
        }
    }

    public function getTotalCities($filter_data = array())
    {
        if (!$filter_data) {
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "city");
        } else {
            if ($filter_data['filter_name']) {
                $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "city where name like '%" . $this->db->escape((string)$filter_data['filter_name']) . "%'";
                $query = $this->db->query($sql);
            } else {
                $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "city");
            }
        }

        return $query->row['total'];
    }
}
