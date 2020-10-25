<?php
/**
 * datetime.php
 *
 * @copyright  2017 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2017-03-24 19:12
 * @modified   2017-03-24 19:12
 */

namespace Utils;

use GeoIp2\Database\Reader;

class Datetime
{
    private $baseTimezone = 'UTC';
    private $toTimezone = 'PRC';
    private $geoReader = null;

    public function __construct($timezone = '')
    {
        $this->geoReader = new Reader(DIR_SYSTEM . 'library/geoip/GeoLite2-City.mmdb');
        if ($timezone) {
            $this->baseTimezone = $timezone;
        }
        if ($ocTimezone = array_get($_COOKIE, 'oc_timezone')) {
            $this->toTimezone = $ocTimezone;
        } elseif ($ocTimezone = $this->getTimeZoneByIP()) {
            $this->toTimezone = $ocTimezone;
        }
    }

    public function getToTimezone()
    {
        return $this->toTimezone;
    }

    public function convert($timeStr, $toTimezone = '', $format = 'Y-m-d H:i:s')
    {
        if (empty($timeStr)) {
            return '';
        }
        if ($toTimezone) {
            $this->toTimezone = $toTimezone;
        }
        $newTimeStr = new \DateTime($timeStr, new \DateTimeZone($this->baseTimezone));
        $newTimeStr->setTimeZone(new \DateTimeZone($this->toTimezone));
        return $newTimeStr->format($format);
    }

    public function getTimeZoneByIP($ip = '')
    {
        if (!$ip) {
            $ip = IP::get();
        }
        try {
            $record = $this->geoReader->city($ip);
            if ($record && $record->location) {
                return $record->location->timeZone;
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }
}
