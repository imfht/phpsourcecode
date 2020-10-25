<?php
namespace Core\Model;
use Think\Model;

class Site extends Model {
    protected $autoCheckFields = false;

    const OPT_TITLE = 'TITLE';
    const OPT_STATCODE = 'STATCODE';
    
    const OPT_CLOSE = 'CLOSE';
    const OPT_CLOSETIPS = 'CLOSETIPS';

    private static function getOptions() {
        $keys = array();
        $keys[] = self::OPT_CLOSE;
        $keys[] = self::OPT_CLOSETIPS;
        $keys[] = self::OPT_TITLE;
        $keys[] = self::OPT_STATCODE;
        return $keys;
    }

    public static function loadSettings($flush = false) {
        $s = C('SITE');
        if(empty($s) || $flush) {
            $keys = self::getOptions();
            $s = Utility::loadSettings('SITE', $keys);
            C('SITE', $s);
        }
    }

    public static function saveSettings($settings) {
        $keys = self::getOptions();
        $settings = coll_elements($keys, $settings);
        return Utility::saveSettings('SITE', $settings);
    }
}
