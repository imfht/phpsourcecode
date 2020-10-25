<?php
namespace MiniFranske\FsMediaGallery\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Frans Saris <franssaris@gmail.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Class that holds slots that are called during install
 */
class Install
{

    /**
     * Add fields to ext:news table when ext:news is installed
     *
     * @param array $sqlString
     * @return array
     */
    public function tablesDefinitionIsBeingBuiltSlot(array $sqlString)
    {

        if (!empty($GLOBALS['TYPO3_LOADED_EXT']['news'])) {
            $sqlString[] = $this->getExtraTableDefinitions();
        }

        return ['sqlString' => $sqlString];
    }

    /**
     * Add extra sql table definitions if ext:news gets installed
     *
     * @param array $sqlString
     * @param $extensionKey
     * @return array
     */
    public function tablesDefinitionIsBeingBuiltForExtension(array $sqlString, $extensionKey)
    {
        if ($extensionKey === 'news'
            || ($extensionKey === 'fs_media_gallery' && !empty($GLOBALS['TYPO3_LOADED_EXT']['news']))
        ) {
            $sqlString[] = $this->getExtraTableDefinitions();
        }
        return ['sqlString' => $sqlString, 'extensionKey' => $extensionKey];
    }

    /**
     * Extra sql table definitions for connecting extension with ext:news
     *
     * @return string
     */
    protected function getExtraTableDefinitions()
    {
        return "
#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (
	related_fsmediaalbums int(11) DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_news_domain_model_news_fsmediaalbums_mm'
#
CREATE TABLE tx_news_domain_model_news_fsmediaalbums_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

";
    }
}

