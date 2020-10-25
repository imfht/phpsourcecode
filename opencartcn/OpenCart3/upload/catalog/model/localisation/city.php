<?php

/**
 * Localisation city Model for frontend.
 *
 * @copyright  2016 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2016-11-10 11:39:20
 * @modified   2016-11-10 11:58:58
 */
class ModelLocalisationCity extends Model
{
    public function getCity($city_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city WHERE city_id = '" . (int)$city_id . "'");

        return $query->row;
    }

    public function getCities()
    {
        $city_data = $this->cache->get('city.status');

        if (!$city_data) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city WHERE status = '1' ORDER BY name ASC");

            $city_data = $query->rows;

            $this->cache->set('city.status', $city_data);
        }

        return $city_data;
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
}
