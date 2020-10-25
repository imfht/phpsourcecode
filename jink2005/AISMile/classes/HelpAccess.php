<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */


class HelpAccessCore
{
    const URL = 'http://help.milebiz.com';

    /**
     * Store in the local database that the user has seen a specific help page
     *
     * @static
     * @param $label
     * @param $version
     */
    public static function trackClick($label, $version)
    {
        Db::getInstance()->execute('
        INSERT INTO `'._DB_PREFIX_.'help_access` (`label`, `version`) VALUES (\''.pSQL($label).'\',\''.pSQL($version).'\')
        ON DUPLICATE KEY UPDATE `version` = \''.pSQL($version).'\'
        ');
    }

    /**
     * Returns the last version seen of a help page seen by the user
     *
     * @static
     * @param $label
     * @return mixed
     */
    public static function getVersion($label)
    {
        return Db::getInstance()->getValue('
        SELECT `version` FROM `'._DB_PREFIX_.'help_access`
        WHERE `label` = \''.pSQL($label).'\'
        ');
    }

    /**
     * Fetch information from the help website in order to know:
     * - if the help page exists
     * - his version
     * - the associated tooltip
     *
     * @static
     * @param $label
     * @param $iso_lang
     * @param $country
     * @param $version
     *
     * @return array
     */
    public static function retrieveInfos($label, $iso_lang, $country, $version)
    {
   	    $url = HelpAccess::URL.'/documentation/renderIcon?label='.$label.'&iso_lang='.$iso_lang.'&country='.$country.'&version='.$version;
        $tooltip = '';

        $ctx = @stream_context_create(array('http' => array('timeout' => 10)));
        $res = @file_get_contents($url, 0, $ctx);

	    $infos = preg_split('/\|/', $res);
	    if (count($infos) > 0)
	    {
            $version = trim($infos[0]);
            if (!empty($version))
            {
                if (count($infos) > 1)
                    $tooltip = trim($infos[1]);
            }
	    }

	    return array('version' => $version, 'tooltip' => $tooltip);
	}
}

