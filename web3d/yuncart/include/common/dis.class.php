<?php

defined('IN_CART') or die;

/**
 *
 * 地区
 * 
 */
class Dis
{

    /**
     *
     * 根据pid获取该pid下属的地区
     * 
     */
    public static function getDistrict($pid = 0, $selected = 0, $type = 'array')
    {
        $districts = DB::getDB()->select("district", "*", "pid='$pid' AND isdel=0", "order", "", "districtid");
        if ($type == 'option') {
            return array2select($districts, "districtid", "district", $selected);
        }
        return $districts;
    }

    /**
     *
     * 根据districtid获取该districtid的zip
     * 
     */
    public static function getZip($districtid = 0)
    {
        $zip = DB::getDB()->selectval("district", "zipcode", "districtid='$districtid'");
        return $zip;
    }

    /**
     *
     * 通过省市区code获取text
     *
     */
    public static function getText($province = "", $city = "", $district = "")
    {
        $text = "";
        $locids = array();
        if ($province)
            $locids[] = $province;
        if ($city)
            $locids[] = $city;
        if ($district)
            $locids[] = $district;
        if ($locids) {
            $loc = DB::getDB()->selectkv("district", "districtid", "district", "districtid in " . cimplode($locids));
            if ($province && isset($loc[$province])) {
                $text .= $loc[$province];
            }
            if ($city && isset($loc[$city])) {
                $text .= $loc[$city];
            }
            if ($district && isset($loc[$district])) {
                $text .= $loc[$district];
            }
        }
        return $text;
    }

}
